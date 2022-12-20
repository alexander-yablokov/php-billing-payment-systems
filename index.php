<?php
header("Content-type: text/html; charset=utf-8");

require_once 'connect_mysql.php';
require_once 'templates.php';
require_once 'reply.php';
require_once 'verification.php';
require_once 'processing.php';
require_once 'logging.php';
require_once 'db.php';

date_default_timezone_set('Asia/Dushanbe');

$command = isset($_GET["command"])?$_GET["command"]:'';
$txn_id = isset($_GET["txn_id"])?$_GET["txn_id"]:'';
$account_id = isset($_GET["account"])?$_GET["account"]:'';
$txn_date = isset($_GET["txn_date"])?$_GET["txn_date"]:'';
$sum = isset($_GET["sum"])?$_GET["sum"]:'';

// Найти лицевой счет ПС по ключу из URL, если существует
$uri = $_SERVER["REQUEST_URI"];
$ps_key = ltrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

try {
    $sql = "SELECT t.account_id,
                   t.payment_system_name
              FROM payment_system_dic t
             where t.payment_system_key=?
               and t.is_deleted=0";
    $ps = db_select($pdo_work, 'find_ps', $sql, [$ps_key]);
    $row = $ps->fetch();
    if ($row) {
        $ps_account = $row['account_id'];
        $ps_name = $row['payment_system_name'];
    } else {
        $comment = 'Не удалось определить платежную систему';
        write_log($comment);
        ps_reply($TEMPLATE["XML_CHECK"], ['comment' => $comment]);
    }

// Обработчик команд
    switch ($command) {
        case 'check':
            check($ps_account, $txn_id, $account_id);
            break;
        case 'pay':
            pay($ps_account, $ps_name, $txn_id, $account_id, $txn_date, $sum);
            break;
        case 'balance':
            balance($ps_account);
            break;
        default:
            $comment = 'Неправильно указано поле command';
            write_log($comment);
            ps_reply($TEMPLATE["XML_CHECK"], ['txn_id' => $txn_id, 'comment' => $comment]);;
    }

} catch (\Exception $e) {
    write_log("ERROR: " . $e->getMessage());
    $comment = 'Ошибка биллинга';
    ps_reply($TEMPLATE["XML_CHECK"], ['txn_id' => $txn_id, 'comment'=>$comment]);
}
