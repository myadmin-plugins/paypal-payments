<?php
/**
 * @return bool|void
 * @throws \Exception
 * @throws \SmartyException
 */
function paypal_refund()
{
	function_requirements('has_acl');
	if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
		dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
		return false;
	}
	require_once __DIR__.'/../paypal_refund.functions.php';
	require_once __DIR__.'/../paypal.functions.inc.php';
	$continue = false;
	if (!isset($GLOBALS['tf']->variables->request['transact_id'])) {
		add_output('Transaction ID is empty!');
		return;
	}
	$desc = "PayPal Payment {$GLOBALS['tf']->variables->request['transact_id']}";
	if (isset($GLOBALS['tf']->variables->request['amount'])) {
		$transactAmount = $GLOBALS['tf']->variables->request['amount'];
	}
	$db = clone $GLOBALS['tf']->db;
	$db->query("SELECT * FROM invoices WHERE invoices_description = '$desc'");
	$checkbox = '';
	if ($db->num_rows() > 0) {
		while ($db->next_record(MYSQL_ASSOC)) {
			$serviceAmount[$db->Record['invoices_id']] = $db->Record['invoices_amount'];
			$checkbox .= '<input type="checkbox" name="refund_amount_opt[]" value="'.$db->Record['invoices_service'].'_'.$db->Record['invoices_id'].'_'.$db->Record['invoices_amount'].'" onclick="return update_partial_payment();" checked>&nbsp;<label for="" style="text-transform: capitalize;"> '.$db->Record['invoices_module'].' '.$db->Record['invoices_service'].' $' .$db->Record['invoices_amount'].'</label><br>';
		}
	}
	if (!isset($GLOBALS['tf']->variables->request['confirmation']) || !verify_csrf('paypal_refund')) {
		$table = new TFTable;
		$table->csrf('paypal_refund');
		$table->set_title('Confirm Refund');
		$table->set_options('cellpadding=10');
		$table->add_hidden('transact_id', $GLOBALS['tf']->variables->request['transact_id']);
		$table->add_hidden('amount', $transactAmount);
		$table->add_field('Services', 'l');
		$table->add_field($checkbox, 'l');
		$table->add_row();
		$table->add_field('Amount To be Refund', 'l');
		$table->add_field($table->make_input('refund_amount', $transactAmount, 25, false, 'id="partialtext"'), 'l');
		$table->add_row();
		$table->add_field('Refund Options', 'l');
		$table->add_field($table->make_radio('refund_opt', 'API', 'API') . 'Adjust the payment invoice', 'l');
		$table->add_row();
		$table->add_field("", 'l');
		$table->add_field($table->make_radio('refund_opt', 'APISCIU') . 'Adjust payment invoice + set charge invoice unpaid', 'l');
		$table->add_row();
		$table->add_field("", 'l');
		$table->add_field($table->make_radio('refund_opt', 'DPIDCI') . 'Delete payment invoice + Delete charge invoice', 'l');
		$table->add_row();
		$table->add_field("", 'l');
		$table->add_field($table->make_radio('refund_opt', 'JRM') . 'Just Refund the money', 'l');
		$table->add_row();
		$table->add_field('Memo', 'l');
		$table->add_field('<textarea rows="4" cols="50" name="memo"></textarea>');
		$table->add_row();
		$table->add_field("&nbsp;");
		$table->add_field("<b>Note: </b> For Partial Refund Memo is required.", 'l');
		$table->add_row();
		$table->add_field('Are you sure want to Refund ?', 'l');
		$table->add_field($table->make_radio('confirmation', 'Yes', 'Yes').'Yes'.$table->make_radio('confirmation', 'No', true).'No', 'l');
		$table->add_row();
		$table->set_colspan(2);
		$table->add_field($table->make_submit('Confirm'));
		$table->add_row();
		add_output($table->get_table());
		$script = '<script>
		function update_partial_payment() {
			var ret = 0;
			$(\'input[type=checkbox]\').each(function () {
				if (this.checked) {
					var gg = $(this).val().split("_");
					ret += parseFloat(gg[2]);
				}
			});
			$(\'#partialtext\').val(ret.toFixed(2));
		}
		</script>';
		add_output($script);
	} elseif (isset($GLOBALS['tf']->variables->request['confirmation']) && $GLOBALS['tf']->variables->request['confirmation'] === 'Yes') {
		if (!empty($GLOBALS['tf']->variables->request['refund_amount_opt'])) {
			$continue = true;
		}
		if ($GLOBALS['tf']->variables->request['refund_amount'] > $GLOBALS['tf']->variables->request['amount']) {
			add_output('Error! You entered Refund amount is greater than invoice amount. Refund amount must be equal or lesser than invoice amount.');
			$continue = false;
		}
		if ($GLOBALS['tf']->variables->request['refund_amount'] <= 0) {
			add_output('Error! You entered Refund amount is less than or equal to $0. Refund amount must be greater than $0.');
			$continue = false;
		}
	}
	$transact_ID = $GLOBALS['tf']->variables->request['transact_id'];
	if ($continue === true && is_paypal_txn_refunded($transact_ID)) {
		add_output('Refund Transaction is already done!');
		$continue = false;
	}
	if ($continue === true) {
		myadmin_log('admin', 'info', 'Going with PayPal Refund', __LINE__, __FILE__);
		foreach ($GLOBALS['tf']->variables->request['refund_amount_opt'] as $values) {
			$explodedValues = explode('_', $values);
			$serviceIds[] = $explodedValues[0];
			$invoiceIds[] = $explodedValues[1];
			$invoiceAmounts[] = $explodedValues[2];
		}
		if ($GLOBALS['tf']->variables->request['amount'] == $GLOBALS['tf']->variables->request['refund_amount']) {
			$refund_type = 'Full';
		} else {
			$refund_type = 'Partial';
		}
		$amount = $GLOBALS['tf']->variables->request['refund_amount'];
		if ($refund_type != 'Full') {
			$memo = $GLOBALS['tf']->variables->request['memo'];
		}
		// Set request-specific fields.
		$transactionID = urlencode($transact_ID);
		$refundType = urlencode($refund_type); // or 'Partial'
		$currencyID = urlencode('USD'); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		// Add request-specific fields to the request string.
		$nvpStr = "&TRANSACTIONID={$transactionID}&REFUNDTYPE={$refundType}&CURRENCYCODE={$currencyID}";
		if (isset($memo)) {
			$nvpStr .= "&NOTE={$memo}";
		}
		if (strcasecmp($refundType, 'Partial') == 0) {
			if ($amount == 0 || !$amount) {
				exit('Partial Refund Amount is not specified.');
			} else {
				$nvpStr .= "&AMT=$amount";
			}
			if (!$memo) {
				exit('Partial Refund Memo is not specified.');
			}
		}
		// Execute the API operation; see the PayPalHttpPost function above.
		$httpParsedResponseAr = PayPalHttpPost('RefundTransaction', $nvpStr, 'live');
		if ('SUCCESS' == mb_strtoupper($httpParsedResponseAr['ACK']) || 'SUCCESSWITHWARNING' == mb_strtoupper($httpParsedResponseAr['ACK'])) {
			//add_output('Refund Completed Successfully: <br />'.print_r($httpParsedResponseAr, TRUE));
			$refundTransactionId = urldecode($httpParsedResponseAr['REFUNDTRANSACTIONID']);
			$refundStatus = urldecode($httpParsedResponseAr['REFUNDSTATUS']);
			$refundFee = urldecode($httpParsedResponseAr['FEEREFUNDAMT']);
			$refundGross = urldecode($httpParsedResponseAr['GROSSREFUNDAMT']);
			$refundNet = urldecode($httpParsedResponseAr['NETREFUNDAMT']);
			$refundTotal = urlencode($httpParsedResponseAr['TOTALREFUNDEDAMOUNT']);
			add_output('Refund Transaction success:<br />Status: '.$refundStatus.'<br/>Transaction Id: '.$refundTransactionId.'<br />Fee Refund Amt: '.$refundFee.'<br />Gross Refund Amt: '.$refundGross.'<br />Net Refund Amt: '.$refundNet.'<br/>Total Refund Amt: '.$refundTotal);
			myadmin_log('admin', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);

			//Invoices Updated
			$db = clone $GLOBALS['tf']->db;
			$dbC = clone $GLOBALS['tf']->db;
			$dbU = clone $GLOBALS['tf']->db;
			$amountRemaining = $amount;
			foreach ($invoiceIds as $inv) {
				$dbC->query("SELECT * FROM invoices WHERE invoices_id = {$inv}");
				if ($dbC->num_rows() > 0) {
					$dbC->next_record(MYSQL_ASSOC);
					$updateInv = $dbC->Record;
					if ($refund_type == 'Full' || $amountRemaining >= $dbC->Record['invoices_amount']) {
						$amount = $dbC->Record['invoices_amount'];
					} else {
						$amount = $amountRemaining;
					}
					$amountRemaining = bcsub($amountRemaining, $amount);
					$invUpdateAmount = bcsub($dbC->Record['invoices_amount'], $amount, 2);
					if ($GLOBALS['tf']->variables->request['refund_opt'] == 'API' || $GLOBALS['tf']->variables->request['refund_opt'] == 'APISCIU') {
						$dbU->query("UPDATE invoices SET invoices_amount={$invUpdateAmount} WHERE invoices_id = {$updateInv['invoices_id']}");
					}
					if ($GLOBALS['tf']->variables->request['refund_opt'] == 'APISCIU') {
						$dbU->query("UPDATE invoices SET invoices_paid = 0 WHERE invoices_id = {$updateInv['invoices_extra']}");
					}

					if ($GLOBALS['tf']->variables->request['refund_opt'] == 'DPIDCI') {
						$dbU->query("UPDATE invoices SET invoices_amount={$invUpdateAmount},invoices_deleted=1 WHERE invoices_id = {$updateInv['invoices_id']}");
						$dbU->query("UPDATE invoices SET invoices_paid = 0,invoices_deleted=1 WHERE invoices_id = {$updateInv['invoices_extra']}");
					}
				}
			}
		} else {
			$errorlongmsg = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
			$errorcode = $httpParsedResponseAr['L_ERRORCODE0'];
			$errorshortmsg = urldecode($httpParsedResponseAr['L_SHORTMESSAGE0']);
			add_output('Refund Transaction failed: <br />Error code: '.$errorcode.'<br />Error short msg: '.$errorshortmsg.'<br />Error Long Message: '.$errorlongmsg);
			myadmin_log('admin', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
		}
	}
}
