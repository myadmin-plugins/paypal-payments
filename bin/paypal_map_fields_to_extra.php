<?php

include __DIR__.'/../../../../include/functions.inc.php';
$db = get_module_db('default');
$db2 = get_module_db('default');
$fields = ['retry_at', 'amount3', 'mc_amount3', 'period3', 'reattempt', 'recurring', 'subscr_date', 'parent_txn_id', 'reason_code'];
$ids = [];
echo "Building ID to Field Data:";
foreach ($fields as $field) {
    echo $field.',';
    $types = $db->qr("select id from paypal where {$field} is not null");
    foreach ($types as $data) {
        if (!array_key_exists($data['id'], $ids)) {
            $ids[$data['id']] = [$field];
        } else {
            $ids[$data['id']][] = $field;
        }
    }
}
echo 'done'.PHP_EOL;
echo 'Found '.count($ids).' IDs To Update'.PHP_EOL;
echo 'Updating IDs:';
foreach ($ids as $id => $data) {
    echo '.';
    $row = $db->qr("select * from paypal where id='{$id}'");
    if (is_null($row['extra'])) {
        $extra = [];
    } else {
        $extra = json_decode($row['extra'], true);
    }
    if (is_array($extra)) {
        foreach ($data as $field) {
            $extra[$field] = $row[$field];
        }
        $extra = $db->real_escape(json_encode($extra));
        $db->query("update paypal set extra='{$extra}' where id='{$id}'");
    } else {
        echo 'Error dealing with ID '.$id.' Extra '.$row['extra'].PHP_EOL;
    }
}
echo 'done'.PHP_EOL;
