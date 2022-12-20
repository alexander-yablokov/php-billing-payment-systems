<?php
function db_select ($pdo, $name, $sql, $params) {
    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        throw new PDOException($name.'sql could not be prepared');
    }
    $result = $stmt->execute($params);
    if (!$result) {
        throw new PDOException($name.' stmt execute result is false');
    }
    return $stmt;
}

function db_insert ($pdo, $name, $sql, $params) {
    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        throw new PDOException($name . ' sql could not be prepared');
    }
    $stmt->execute($params);
}