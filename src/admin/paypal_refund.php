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
	if (isset($GLOBALS['tf']->variables->request['amount']))
		$transactAmount = $GLOBALS['tf']->variables->request['amount'];
	$db = clone $GLOBALS['tf']->db;
	$db->query("SELECT * FROM invoices WHERE invoices_description = '$desc'");
	$select_serv = '<select name="refund_amount_opt" onchange="update_partial_row()">';
	$select_serv .= '<optgroup label="Refund All Services">';
	$select_serv .= '<option value="Full">$'.$transactAmount.' Refund Full Amount</option>';
	$select_serv .= '</optgroup>';
	$select_serv .= '<optgroup label="Refund Any Service">';
	if ($db->num_rows() > 0) {
		while ($db->next_record(MYSQL_ASSOC)) {
			$serviceAmount[$db->Record['invoices_id']] = $db->Record['invoices_amount'];
			$select_serv .= '<option value="'.$db->Record['invoices_service'].'_'.$db->Record['invoices_id'].'_'.$db->Record['invoices_amount'].'">'.$db->Record['invoices_module'].' '.$db->Record['invoices_service'].' $'.$db->Record['invoices_amount'].'</option>';
		}
	}
	$select_serv .= '</optgroup>';
	$select_serv .= '</select>';
	if (!isset($GLOBALS['tf']->variables->request['confirmation']) || !verify_csrf('paypal_refund')) {
		$table = new TFTable;
		$table->csrf('paypal_refund');
		$table->set_title('Confirm Refund');
		$table->set_options('cellpadding=10');
		$table->add_hidden('transact_id', $GLOBALS['tf']->variables->request['transact_id']);
		$table->add_hidden('amount', $transactAmount);
		$table->add_field('Services', 'l');
		$table->add_field($select_serv, 'l');
		$table->add_row();
		$table->add_field('Amount To be Refund', 'l');
		$table->add_field($table->make_input('refund_amount',$transactAmount,25,false,'id="partialtext"'), 'l');
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
		$table->add_field("<b>Note: </b> For Partial Refund Memo is required.",'l');
		$table->add_row();
		$table->add_field('Are you sure want to Refund ?', 'l');
		$table->add_field($table->make_radio('confirmation', 'Yes', false).'Yes'.$table->make_radio('confirmation', 'No', true).'No', 'l');
		$table->add_row();
		$table->set_colspan(2);
		$table->add_field($table->make_submit('Confirm'));
		$table->add_row();
		add_output($table->get_table());
		$script = '<script>
		$(function(){
			update_partial_row();
		});
		function update_partial_row() {
			opt_val = $("select[name=\'refund_amount_opt\']").val();
			if(opt_val == \'Full\') {
				$("select[name=\'refund_amount_opt\']").parents("tr").next().hide();
			} else {
				selectedAmount = opt_val.split("_");
				$("#partialtext").val(selectedAmount[2]);
				$("select[name=\'refund_amount_opt\']").parents("tr").next().show();
			}
		}
		</script>';
		add_output($script);
	} elseif (isset($GLOBALS['tf']->variables->request['confirmation']) && $GLOBALS['tf']->variables->request['confirmation'] === 'Yes') {
		if ($GLOBALS['tf']->variables->request['refund_amount_opt'] == 'Full') {
			$continue = true;
		} else {
			list($serviceId, $invoiceId, $invoiceAmount) = explode('_', $GLOBALS['tf']->variables->request['refund_amount_opt']);
			if ($invoiceAmount >= $GLOBALS['tf']->variables->request['refund_amount']) {
				$continue = true;
			} else {
				add_output('Error! You entered Refund amount is greater than invoice amount. Refund amount must be equal or lesser than invoice amount.');
				return;
			}
		}
	}
	$transact_ID = $GLOBALS['tf']->variables->request['transact_id'];
	if ($continue === true && is_paypal_txn_refunded($transact_ID)) {
		add_output('Refund Transaction is already done!');
		$continue = false;
	}
	if ($continue === true) {
		myadmin_log('admin', 'info', 'Going with PayPal Refund', __LINE__, __FILE__);
		if ((isset($GLOBALS['tf']->variables->request['refund_amount_opt']) && $GLOBALS['tf']->variables->request['refund_amount_opt'] == 'Full') || $transactAmount == $GLOBALS['tf']->variables->request['refund_amount']) {
			$refund_type = 'Full';
		} else {
			$refund_type = 'Partial';
		}
		$amount = $GLOBALS['tf']->variables->request['refund_amount_opt'] == 'Full' ? $transactAmount : $GLOBALS['tf']->variables->request['refund_amount'];
		if ($GLOBALS['tf']->variables->request['refund_amount_opt'] != 'Full')
			$memo = $GLOBALS['tf']->variables->request['memo'];
		// Set request-specific fields.
		$transactionID = urlencode($transact_ID);
		$refundType = urlencode($refund_type); // or 'Partial'
			//$amount;                          // required if Partial.
			//$memo;                            // required if Partial.
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
			if ($GLOBALS['tf']->variables->request['refund_amount_opt'] != 'Full')
				$invoices = [$invoiceId];
			elseif ($GLOBALS['tf']->variables->request['refund_amount_opt'] == 'Full')
				$invoices = array_keys($serviceAmount);
            
            foreach ($invoices as $inv) {
				$dbC->query("SELECT * FROM invoices WHERE invoices_id = {$inv}");
				if($dbC->num_rows() > 0) {
					$dbC->next_record(MYSQL_ASSOC);
					$updateInv = $dbC->Record;
					if ($GLOBALS['tf']->variables->request['refund_amount_opt'] == 'Full')
						$amount = $dbC->Record['invoices_amount'];
					$invUpdateAmount = bcsub($dbC->Record['invoices_amount'], $amount, 2);
					if($GLOBALS['tf']->variables->request['refund_opt'] == 'API' || $GLOBALS['tf']->variables->request['refund_opt'] == 'APISCIU')
						$dbU->query("UPDATE invoices SET invoices_amount={$invUpdateAmount} WHERE invoices_id = {$updateInv['invoices_id']}");
					if($GLOBALS['tf']->variables->request['refund_opt'] == 'APISCIU')
						$dbU->query("UPDATE invoices SET invoices_paid = 0 WHERE invoices_id = {$updateInv['invoices_extra']}");

					if($GLOBALS['tf']->variables->request['refund_opt'] == 'DPIDCI') {
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
