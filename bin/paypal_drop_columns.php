<?php
include 'include/functions.inc.php';
$db = get_module_db('default');
$db2 = get_module_db('default');
$fields = ['receipt_id', 'echeck_time_processed', 'correlation_id', 'test_ipn', 'mc_gross_1', 'resend', 'contact_phone', 'locked', 'for_auction', 'item_number1', 'auction_closing_date', 'auction_buyer_id', 'num_cart_items', 'item_name1', 'quantity1', 'memo', 'ebay_txn_id1', 'payment_cycle', 'next_payment_date', 'initial_payment_amount', 'currency_code', 'time_created', 'period_type', 'product_type', 'amount_per_cycle', 'profile_status', 'amount', 'outstanding_balance', 'recurring_payment_id', 'product_name', 'pending_reason', 'case_id', 'case_type', 'case_creation_date', 'item_mpn1', 'item_count_unit1', 'item_tax_rate1', 'mc_shipping', 'item_tax_rate_double1', 'mc_handling', 'mc_handling1', 'mc_shipping1', 'item_style_number1', 'tax1', 'item_plu1', 'item_isbn1', 'item_model_number1', 'item_taxable1', 'recur_times', 'subscr_effective', 'item_number2', 'mc_handling2', 'mc_shipping2', 'tax2', 'item_name2', 'quantity2', 'mc_gross_2', 'buyer_additional_information', 'amount1', 'mc_amount1', 'period1', 'exchange_rate', 'settle_amount', 'settle_currency', 'initial_payment_status', 'initial_payment_txn_id', 'mc_tax1', 'invoice', 'fulfillment_address_name', 'fulfillment_address_line1', 'fulfillment_address_line2', 'fulfillment_address_city', 'fulfillment_address_zip', 'fulfillment_address_country', 'fulfillment_address_state', 'fulfillment_address_country_code', 'fulfillment_order_reference_number', 'ebay_txn_id2', 'auction_multi_item'];
$parts = [];
foreach ($fields as $field)
	$parts[] = 'drop column '.$field;
$query = 'alter table paypal '.implode(', ', $parts).';';
echo $query.PHP_EOL;
