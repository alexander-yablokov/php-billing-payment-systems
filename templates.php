<?php
    $TEMPLATE["XML_CHECK"] = <<<EOF
    <?xml version="1.0" encoding="UTF-8"?> 
    <response> 
    <osmp_txn_id>[OSMP_TXN_ID]</osmp_txn_id> 
    <result>[RESULT]</result> 
    <comment>[COMMENT]</comment> 
    </response> 
    EOF;
    
    $TEMPLATE["XML_PAY"] = <<<EOF
    <?xml version="1.0" encoding="UTF-8"?> 
    <response> 
    <osmp_txn_id>[OSMP_TXN_ID]</osmp_txn_id> 
    <result>[RESULT]</result>
    <comment>[COMMENT]</comment>
    <sum>[SUM]</sum>  
    </response> 
    EOF;
    
    $TEMPLATE["XML_BALANCE_PS"] = <<<EOF
    <?xml version="1.0" encoding="UTF-8"?> 
    <response> 
    <result>[RESULT]</result> 
    <balance>[BALANCE]</balance> 
    <comment>[COMMENT]</comment> 
    </response> 
    EOF;
