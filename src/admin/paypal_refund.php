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
	if (isset($GLOBALS['tf']->variables->request['confirmed']) && $GLOBALS['tf']->variables->request['confirmed'] == 'yes') {
		$continue = true;
		$transact_ID = $GLOBALS['tf']->variables->request['transact_id'];
		if ($GLOBALS['tf']->variables->request['refund_amount'] <= 0) {
			add_output('<div class="alert alert-danger">Error! You entered Refund amount less than or equal to $0. Refund amount must be greater than $0.</div>');
			$continue = false;
		}
		foreach ($GLOBALS['tf']->variables->request['refund_amount_opt'] as $values) {
			$explodedValues = explode('_', $values);
			$invoiceIds[] = $explodedValues[1];
			$invoiceAmounts[] = $explodedValues[2];
		}
		if ($GLOBALS['tf']->variables->request['amount'] < $GLOBALS['tf']->variables->request['refund_amount']) {
			add_output('<div class="alert alert-danger">Error! Refund amount greater than paid amount, must be lesser or equal.</div>');
			$continue = false;
		}
		if ($continue === true && is_paypal_txn_refunded($transact_ID)) {
			add_output('Refund Transaction is already done!');
			$continue = false;
		}
		if ($continue === true) {
			myadmin_log('admin', 'info', 'Going with PayPal Refund', __LINE__, __FILE__);
			$amount = $GLOBALS['tf']->variables->request['refund_amount'];
			myadmin_log('admin', 'info', 'Refund amount : '.$amount, __LINE__, __FILE__);
			if ($GLOBALS['tf']->variables->request['amount'] == $GLOBALS['tf']->variables->request['refund_amount']) {
				$refund_type = 'Full';
			} else {
				$refund_type = 'Partial';
			}
			if ($refund_type != 'Full') {
				$memo = $GLOBALS['tf']->variables->request['memo'];
			}
			$transactionID = urlencode($transact_ID);
			$refundType = urlencode($refund_type);
			$currencyID = urlencode('USD');
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
			$httpParsedResponseAr = PayPalHttpPost('RefundTransaction', $nvpStr, 'live');
			if ('SUCCESS' == mb_strtoupper($httpParsedResponseAr['ACK']) || 'SUCCESSWITHWARNING' == mb_strtoupper($httpParsedResponseAr['ACK'])) {
				$refundTransactionId = urldecode($httpParsedResponseAr['REFUNDTRANSACTIONID']);
				$refundStatus = urldecode($httpParsedResponseAr['REFUNDSTATUS']);
				$refundFee = urldecode($httpParsedResponseAr['FEEREFUNDAMT']);
				$refundGross = urldecode($httpParsedResponseAr['GROSSREFUNDAMT']);
				$refundNet = urldecode($httpParsedResponseAr['NETREFUNDAMT']);
				$refundTotal = urlencode($httpParsedResponseAr['TOTALREFUNDEDAMOUNT']);
				add_output('<div class="alert alert-success">Refund Transaction success:<br />Status: '.$refundStatus.'<br/>Transaction Id: '.$refundTransactionId.'<br />Fee Refund Amt: '.$refundFee.'<br />Gross Refund Amt: '.$refundGross.'<br />Net Refund Amt: '.$refundNet.'<br/>Total Refund Amt: '.$refundTotal.'</div>');
				myadmin_log('admin', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
				$db = clone $GLOBALS['tf']->db;
				$dbC = clone $GLOBALS['tf']->db;
				$dbU = clone $GLOBALS['tf']->db;
				$now = mysql_now();
				$amountRemaining = $amount;
				myadmin_log('admin', 'info', 'Paypal Refund invoice Ids - '.json_encode($invoiceIds), __LINE__, __FILE__);
				$invoice = new \MyAdmin\Orm\Invoice($db);
				$invTotal = count($invoiceIds);
				$invLoop = 0;
				foreach ($invoiceIds as $inv) {
					$dbC->query("SELECT * FROM invoices WHERE invoices_id = {$inv}");
					if ($dbC->num_rows() > 0) {
						$dbC->next_record(MYSQL_ASSOC);
						$updateInv = $dbC->Record;
						if (++$invLoop == $invTotal) {
							$amount = $amountRemaining;
						} elseif ($refund_type == 'Full' || $amountRemaining >= $dbC->Record['invoices_amount']) {
							$amount = $dbC->Record['invoices_amount'];
						} else {
							$amount = $amountRemaining;
						}
						$amountRemaining = bcsub($amountRemaining, $amount);
						$invUpdateAmount = bcsub($dbC->Record['invoices_amount'], $amount, 2);
						$invoice->setDescription("REFUND: {$updateInv['invoices_description']}")
							->setAmount($amount)
							->setCustid($updateInv['invoices_custid'])
							->setType(2)
							->setDate($now)
							->setGroup(0)
							->setDueDate($now)
							->setExtra($inv)
							->setService($updateInv['invoices_service'])
							->setPaid(0)
							->setModule($updateInv['invoices_module'])
							->save();
						if ($GLOBALS['tf']->variables->request['unpaid'] == 'yes') {
							$dbU->query("UPDATE invoices SET invoices_paid = 0 WHERE invoices_id = {$updateInv['invoices_extra']}");
						}
						$db->query(make_insert_query('history_log', [
							'history_id' => null,
							'history_sid' => $GLOBALS['tf']->session->sessionid,
							'history_timestamp' => mysql_now(),
							'history_creator' => $GLOBALS['tf']->session->account_id,
							'history_owner' => $updateInv['invoices_custid'],
							'history_section' => 'cc_refund',
							'history_type' => $transact_ID,
							'history_new_value' => "Refunded {$amount}",
							'history_old_value' => "Invoice Amount {$dbC->Record['invoices_amount']}"
						]), __LINE__, __FILE__);
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
	$table = new TFTable;
	$table->csrf('paypal_refund');
	$table->set_title('Confirm Refund');
	$table->set_form_options('id="paypalrefundform"');
	$table->set_options('cellpadding=10');
	$table->add_hidden('transact_id', $GLOBALS['tf']->variables->request['transact_id']);
	$table->add_hidden('amount', $transactAmount);
	$table->add_field('Services', 'l');
	$table->add_field($checkbox, 'l');
	$table->add_row();
	$table->add_field('Amount To be Refund', 'l');
	$table->add_field($table->make_input('refund_amount', $transactAmount, 25, false, 'id="partialtext"'), 'l');
	$table->add_row();
	$table->add_field('Memo', 'l');
	$table->add_field('<textarea rows="4" cols="50" name="memo"></textarea><br><b>Note: </b> For Partial Refund Memo is required.', 'l');
	$table->add_row();
	$table->add_field('Set Charge Invoice Unpaid', 'l');
	$table->add_field($table->make_radio('unpaid', 'yes').' Yes&nbsp; '.$table->make_radio('unpaid', 'no', 'no').' No', 'l');
	$table->add_row();
	$table->set_colspan(2);
	$table->add_hidden('confirmed', 'yes');
	$table->add_field($table->make_submit('Submit','submit','confirm','onclick="return confirm_dialog(event);"'));
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
	function confirm_dialog(event) {
		event.preventDefault();
		var c = confirm("Are you sure want to refund?");
		if(c){
			$("form#paypalrefundform").submit();
		  }
	}
	</script>';
	add_output($script);
}
