<?php

include __DIR__.'/../../../../include/functions.inc.php';
$db = get_module_db('default');
$db2 = get_module_db('default');
$fields = [];
$types = $db->qr("select * from paypal limit 1");
$total = $db->qr("select count(*) as counter from paypal");
$total = $total['counter'];
echo "Getting Field Usage Counts for {$total} Transactions\n";
echo "Counting Field: ";
foreach ($types as $type => $value) {
    if (in_array($type, ['id','txn_id','when','custid','lid','txn_type','mc_gross','payment_date','payment_status','payer_status','payer_email','payer_id','notify_version','charset','verify_sign'])) {
        continue;
    }
    echo $type.',';
    $count = $db->qr("select count(*) as counter from paypal where {$type} is not null");
    $fields[$type] = $count['counter'];
}
echo 'done'.PHP_EOL;
