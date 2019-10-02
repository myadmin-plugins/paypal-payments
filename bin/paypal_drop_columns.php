<?php
include __DIR__.'/../../../../include/functions.inc.php';
$db = get_module_db('default');
$db2 = get_module_db('default');
$fields = ['retry_at', 'amount3', 'mc_amount3', 'period3', 'reattempt', 'recurring', 'subscr_date', 'parent_txn_id', 'reason_code'];
$parts = [];
foreach ($fields as $field) {
	$parts[] = 'drop column '.$field;
}
$query = 'alter table paypal '.implode(', ', $parts).';';
echo $query.PHP_EOL;
