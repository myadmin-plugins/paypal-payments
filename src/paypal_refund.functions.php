<?php
/**
 * PayPal Related Functionality
 * Last Changed: $LastChangedDate: 2016-11-27 07:44:43 -0500 (Sun, 27 Nov 2016) $
 * @author detain
 * @copyright 2017
 * @package MyAdmin
 * @category PayPal
 */

/**
 * Send HTTP POST Request
 *
 * @param $methodName_
 * @param $nvpStr_
 * @param $env
 * @return array Parsed HTTP Response body
 * @internal param \The $string API method name
 * @internal param \The $string POST Message fields in &name=value pair format
 */
function PPHttpPost($methodName_, $nvpStr_, $env = 'live') {
	$API_UserName = urlencode(PAYPAL_API_USERNAME);
	$API_Password = urlencode(PAYPAL_API_PASSWORD);
	$API_Signature = urlencode(PAYPAL_API_SIGNATURE);
	if('sandbox' === $env)
		$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
	else
		$API_Endpoint = 'https://api-3t.paypal.com/nvp';
	$version = urlencode('119');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	$httpResponse = curl_exec($ch);
	if(!$httpResponse)
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	myadmin_log('billing', 'info', $httpResponse, __LINE__, __FILE__);
	$httpResponseAr = explode('&', $httpResponse);
	$httpParsedResponseAr = [];
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode('=', $value);
		if(count($tmpAr) > 1)
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
	}
	if((0 == count($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr))
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	return $httpParsedResponseAr;
}

/**
 * @param null $transactionId
 * @return mixed
 */
function refundPaypalTransaction($transactionId = null) {
	require_once __DIR__.'/paypal.functions.inc.php';
	if($transactionId === null) {
		$result['status'] = 'Failed';
		$result['msg'] = 'Transaction ID is empty!';
		return $result;
	}
	if(is_paypal_txn_refunded($transactionId)) {
		$result['status'] = 'Failed';
		$result['msg'] = 'Refund Transaction is already done!';
		return $result;
	}

	// Set request-specific fields.
	$transactionID = urlencode($transactionId);
	$refundType = urlencode('Full');
	$currencyID = urlencode('USD');   // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

	// Add request-specific fields to the request string.
	$nvpStr = "&TRANSACTIONID={$transactionID}&REFUNDTYPE={$refundType}&CURRENCYCODE={$currencyID}";
	myadmin_log('billing', 'info', 'Going with PayPal Refund of '.$transactionID, __LINE__, __FILE__);
	// Execute the API operation; see the PPHttpPost function above.
	$httpParsedResponseAr = PPHttpPost('RefundTransaction', $nvpStr);
	if('SUCCESS' == mb_strtoupper($httpParsedResponseAr['ACK']) || 'SUCCESSWITHWARNING' == mb_strtoupper($httpParsedResponseAr['ACK'])) {
		$result['status'] = 'Success';
		$result['msg'] = 'Refund Transaction is completed';
		$result['refundTransactionId'] = urldecode($httpParsedResponseAr['REFUNDTRANSACTIONID']);
		$result['refundStatus'] = urldecode($httpParsedResponseAr['REFUNDSTATUS']);
		$result['refundFeeAmt'] = urldecode($httpParsedResponseAr['FEEREFUNDAMT']);
		$result['refundGrossAmt'] = urldecode($httpParsedResponseAr['GROSSREFUNDAMT']);
		$result['refundNetAmt'] = urldecode($httpParsedResponseAr['NETREFUNDAMT']);
		$result['refundTotalAmt'] = urlencode($httpParsedResponseAr['TOTALREFUNDEDAMOUNT']);
		myadmin_log('billing', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
		return $result;
	} else  {
		$result['status'] = 'Failed';
		$result['msg'] = 'Refund Transaction is failed from paypal side';
		$result['errorlongmsg']  = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
		$result['$errorcode']  = $httpParsedResponseAr['L_ERRORCODE0'];
		$result['$errorshortmsg']  = urldecode($httpParsedResponseAr['L_SHORTMESSAGE0']);
		myadmin_log('billing', 'info', json_encode($httpParsedResponseAr), __LINE__, __FILE__);
		return $result;
	}
}
