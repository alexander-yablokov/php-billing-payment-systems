<?php
    function ps_reply ($template, $params) {
        $txn_id=isset($params['txn_id'])?$params['txn_id']:'txn_id';
        $result=isset($params['result'])?$params['result']:'300';
        $comment=isset($params['comment'])?$params['comment']:'comment';
 //       $pay_tran_id=isset($params['pay_tran_id'])?$params['pay_tran_id']:'pay_tran_id';
        $sum=isset($params['sum'])?$params['sum']:'sum';
        $balance=isset($params['balance'])?$params['balance']:'balance';

        $replace = array("[OSMP_TXN_ID]" => $txn_id,
                         "[RESULT]" => $result,
                         "[COMMENT]" => $comment,
                         "[PAY_TRAN_ID]" => $pay_tran_id,
                         "[SUM]" => $sum,
                         "[BALANCE]" => $balance);
        echo strtr($template,$replace);
        exit;
    }
