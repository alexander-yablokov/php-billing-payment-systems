<?php
    function check($ps_account, $txn_id, $account_id) {
        global $TEMPLATE, $pdo_work;

        $result = 0;
        $comment = '';
        if (!verify_txn($txn_id)) {
            $result = 300;
            $comment .= 'Неправильно указано поле - txn_id - идентификатор платежа. ';
        } elseif (has_duplicates($pdo_work, $ps_account, $txn_id)) {
            $result = 300;
            $comment .= "Платеж с указанным txn_id=$txn_id уже зарегистрирован. ";
        }

        if (!verify_txn($account_id)) {
            $result = 300;
            $comment .= 'Неправильно указано поле - account - лицевой счет. ';
        }

        if ($result === 0) {
            $sql = "SELECT count(*) as accounts_qty
                         FROM accounts a
                         WHERE a.id = ?
                         AND a.is_deleted=0";
            $accounts = db_select($pdo_work, 'check', $sql, [$account_id]);
            $row = $accounts->fetch();
            $result = ($row['accounts_qty'] == 1)?0:300;
            $comment = ($result==0)?'Успешно':"Лицевой счет $account_id не существует";
        }
        ps_reply($TEMPLATE["XML_CHECK"], ['result'=>$result,
                                          'txn_id'=>$txn_id,
                                          'comment'=>$comment]);
    }

    function pay($ps_account, $ps_name, $txn_id, $account_id, $txn_date, $sum) {
        global $TEMPLATE, $pdo_work;

        $result = 0;
        $comment = '';
        if (!verify_txn($txn_id)) {
            $result = 300;
            $comment .= 'Неправильно указано поле - txn_id - идентификатора платежа. ';
        } elseif (has_duplicates($pdo_work, $ps_account, $txn_id)) {
            $result = 300;
            $comment .= 'Платеж с указанным txn_id уже зарегистрирован. ';
        }
        if (!verify_account($account_id)) {
            $result = 300;
            $comment .= 'Неправильно указано поле - account - лицевой счет. ';
        }
        $unix_date = convert_date($txn_date);
        if (!$unix_date) {
            $result = 300;
            $comment .= 'Неправильно указано поле - txn_date - дата платежа. ';
        }
        if (!verify_sum($sum)) {
            $result = 300;
            $comment .= 'Неправильно указано поле - sum - сумма платежа. ';
        }
        if ($result === 0) {
            try {
                $pdo_work->beginTransaction();
                $ps_comments = "Платеж проведен через систему $ps_name";
                $sql =  "INSERT INTO UTM5.payments_queue
                            (account_id, payment_date, payment_sum, payment_type,
                             who_receive, doc_num, comments_for_admin, comments_for_user,
                             is_locked, is_deleted)
                     VALUES (?, ?, ?, ?,
                             ?, ?, ?, ?,
                             ?, ?)";
// Зачисляем на ЛС абонента
                db_insert($pdo_work, 'insert_queue', $sql, [$account_id, $unix_date, $sum, 104,
                    -95, $txn_id, $ps_comments, $ps_comments,
                    0, 0]);
// Списываем средства с ЛС платежной системы
                db_insert($pdo_work, 'insert_queue', $sql, [$ps_account, $unix_date, -$sum, 104,
                    -95, $txn_id, $ps_comments, $ps_comments,
                    0, 0]);
// Получить ID платежа в биллинге - пока исключим
                $pdo_work->commit();
                $result = 0;
                $comment = 'Успешно';
            } catch (\Exception $e) {
                $pdo_work->rollBack();
                throw new Exception($e->getMessage());
            }
        }
        ps_reply($TEMPLATE["XML_PAY"], ['result' => $result,
                                        'comment' => $comment,
                                        'txn_id' => $txn_id,
                                        'sum' => $sum]);
    }

    function balance($ps_account_id) {
        global $TEMPLATE, $pdo_work;
        $sql = "SELECT a.balance
                  FROM accounts a
                 where a.id = ?";
        $data = db_select($pdo_work, 'ps_balance', $sql, [$ps_account_id]);
        $row = $data->fetch();
        $ps_balance = $row['balance'];
        ps_reply($TEMPLATE["XML_BALANCE_PS"], ['result' => 0,
                 'comment' => 'Успешно',
                 'balance' => round($ps_balance,2)]);
    }