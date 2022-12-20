<?php
// Рабочая БД
$host_work = '';
$db_work  = '';
$user_work = '';
$pass_work = '';
$port_work = "";
$charset_work = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn_work = "mysql:host=$host_work;dbname=$db_work;charset=$charset_work;port=$port_work";

try {
    $pdo_work = new PDO($dsn_work, $user_work, $pass_work, $options);
} catch (Exception $e) {
    log(" Could not connect to database: ".$e->getMessage());
    exit;
}

