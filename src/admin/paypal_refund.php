<?php
/**
 * @return bool|void
 * @throws \Exception
 * @throws \SmartyException
 */
function paypal_refund() {
		function_requirements('has_acl');
		if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
			dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
			return false;
		}
		require_once __DIR__.'/../paypal_refund.functions.php';
		require_once __DIR__.'/../paypal.functions.inc.php';
		$continue = false;
		if(!isset($GLOBALS['tf']->variables->request['transact_id'])) {
			add_output('Transaction ID is empty!');
			return;
		}
		if(!isset($GLOBALS['tf']->variables->request['confirmation']) || !verify_csrf('paypal_refund')) {
			$table = new TFTable;
			$table->csrf('paypal_refund');
			$table->set_title('Confirm Refund');
			$table->set_options('cellpadding=10');
			$table->add_hidden('transact_id', $GLOBALS['tf']->variables->request['transact_id']);
			$table->add_field('Are you sure want to Refund ?', 'l');
			$table->add_field($table->make_radio('confirmation', 'Yes', FALSE) . 'Yes' . $table->make_radio('confirmation', 'No', TRUE) . 'No', 'l');
			$table->add_row();
			$table->set_colspan(2);
			$table->add_field($table->make_submit('Confirm'));
			$table->add_row();
			add_output($table->get_table());
		} elseif(isset($GLOBALS['tf']->variables->request['confirmation']) && $GLOBALS['tf']->variables->request['confirmation'] === 'Yes')
			$continue = true;
		$transact_ID = $GLOBALS['tf']->variables->request['transact_id'];
		if($continue === true && is_paypal_txn_refunded($transact_ID)) {
			add_output('Refund Transaction is already done!');
			$continue = false;
		}
		if ($continue === true) {
			myadmin_log('admin', 'info', 'Going with PayPal Refund', __LINE__, __FILE__);
			if(isset($GLOBALS['tf']->variables->request['refund_type']))
				$refund_type = $GLOBALS['tf']->variables->request['refund_type'];
			else
				$refund_type = 'Full';
			if (isset($GLOBALS['tf']->variables->request['amount']))
				$amount = $GLOBALS['tf']->variables->request['amount'];
			if (isset($GLOBALS['tf']->variables->request['memo']))
				$memo = $GLOBALS['tf']->variables->request['memo'];
			// Set request-specific fields.
			$transactionID = urlencode($transact_ID);
			$refundType = urlencode($refund_type);  // or 'Partial'
			//$amount;                          // required if Partial.
			//$memo;                            // required if Partial.
			$currencyID = urlencode('USD');   // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
			// Add request-specific fields to the request string.
			$nvpStr = "&TRANSACTIONID={$transactionID}&REFUNDTYPE={$refundType}&CURRENCYCODE={$currencyID}";
			if(isset($memo))
				$nvpStr .= "&NOTE={$memo}";
			if(strcasecmp($refundType, 'Partial') == 0) {
				if(!isset($amount))
					exit('Partial Refund Amount is not specified.');
				else
					$nvpStr .= "&AMT=$amount";
				if(!isset($memo))
					exit('Partial Refund Memo is not specified.');
			}
			// Execute the API operation; see the PayPalHttpPost function above.
			$httpParsedResponseAr = PayPalHttpPost('RefundTransaction', $nvpStr, 'live');
			if('SUCCESS' == mb_strtoupper($httpParsedResponseAr['ACK']) || 'SUCCESSWITHWARNING' == mb_strtoupper($httpParsedResponseAr['ACK'])) {
				//add_output('Refund Completed Successfully: <br />'.print_r($httpParsedResponseAr, TRUE));
				$refundTransactionId = urldecode($httpParsedResponseAr['REFUNDTRANSACTIONID']);
				$refundStatus = urldecode($httpParsedResponseAr['REFUNDSTATUS']);
				$refundFee = urldecode($httpParsedResponseAr['FEEREFUNDAMT']);
				$refundGross = urldecode($httpParsedResponseAr['GROSSREFUNDAMT']);
				$refundNet = urldecode($httpParsedResponseAr['NETREFUNDAMT']);
				$refundTotal = urlencode($httpParsedResponseAr['TOTALREFUNDEDAMOUNT']);
				add_output('Refund Transaction success:<br />Status: '.$refundStatus.'<br/>Transaction Id: '.$refundTransactionId.'<br />Fee Refund Amt: '. $refundFee.'<br />Gross Refund Amt: '.$refundGross.'<br />Net Refund Amt: '.$refundNet.'<br/>Total Refund Amt: '.$refundTotal);
				myadmin_log('admin', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
			} else {
				$errorlongmsg = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
				$errorcode = $httpParsedResponseAr['L_ERRORCODE0'];
				$errorshortmsg = urldecode($httpParsedResponseAr['L_SHORTMESSAGE0']);
				add_output('Refund Transaction failed: <br />Error code: ' .$errorcode.'<br />Error short msg: '.$errorshortmsg.'<br />Error Long Message: '.$errorlongmsg);
				myadmin_log('admin', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
			}
		}
	}
