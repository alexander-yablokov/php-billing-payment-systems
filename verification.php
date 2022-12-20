<?php
    function verify_txn($txn_id) {
        return $txn_id && preg_match('/^[1-9][0-9]{0,19}$/', $txn_id);
    }

    function verify_account($account_id) {
        return $account_id && preg_match('/^[1-9][0-9]{0,199}$/', $account_id);
    }

    function verify_sum($sum) {
        return $sum && preg_match('/^(0|[1-9][0-9]{0,5})(\.[0-9]{1,2})?$/', $sum);
    }

    function convert_date($txn_date) {
        if ($txn_date && preg_match('/^(20[0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/', $txn_date, $regs)) {
            $year = $regs[1];
            $month = $regs[2];
            $day = $regs[3];
            $hour = $regs[4];
            $min = $regs[5];
            $sec = $regs[6];
            // дата платежа в UNIX формате
            return mktime($hour, $min, $sec, $month, $day, $year);
        } else {
            return 0;
        }
    }

    function has_duplicates($pdo_work, $ps_account_id, $txn_id) {
        $sql = 'SELECT count(*) as dpl_qty
                  FROM payments_queue
                 WHERE account_id = ?
                   AND doc_num = ?';
        $dbl = db_select($pdo_work, 'duplicates', $sql, [$ps_account_id, $txn_id]);
        $row = $dbl->fetch();
        return $row['dpl_qty'];
    }

