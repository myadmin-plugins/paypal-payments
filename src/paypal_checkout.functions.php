<?php
include_once __DIR__.'/../../config/config.settings.php';
$SandboxFlag = false;
$sBNCode = 'PP-ECWizard';
if ($SandboxFlag == true) {
	$API_UserName=PAYPAL_SANDBOX_API_USERNAME;
	$API_Password=PAYPAL_SANDBOX_API_PASSWORD;
	$API_Signature=PAYPAL_SANDBOX_API_SIGNATURE;
	$API_Endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
	$PAYPAL_URL = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';
	$PAYPAL_DG_URL = 'https://www.sandbox.paypal.com/incontext?token=';
} else {
	$API_UserName=PAYPAL_API_USERNAME;
	$API_Password=PAYPAL_API_PASSWORD;
	$API_Signature=PAYPAL_API_SIGNATURE;
	$API_Endpoint = 'https://api-3t.paypal.com/nvp';
	$PAYPAL_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
	$PAYPAL_DG_URL = 'https://www.paypal.com/incontext?token=';
}
$USE_PROXY = false;
$version = '109.0';

if (session_id() == '')
	session_start();

/**
 * @param $paymentAmount
 * @param $currencyCodeType
 * @param $paymentType
 * @param $returnURL
 * @param $cancelURL
 * @param $items
 * @param int $period
 * @param bool $repeat_amount
 * @param string $category
 * @param string $custom
 * @return array
 */
function SetSubscriptionExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $period = 1, $repeat_amount = false, $category = 'Physical', $custom = '') {
	if ($repeat_amount == false)
		$repeat_amount = $paymentAmount;
	//Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
	$nvpstr = '';
	$nvpstr .= '&RETURNURL='.$returnURL;
	$nvpstr .= '&CANCELURL='.$cancelURL;
	$nvpstr .= '&REQCONFIRMSHIPPING=0';
	$nvpstr .= '&NOSHIPPING=1';
	//$nvpstr .= "&PAYMENTREQUEST_0_AMT=". $repeat_amount;
	$nvpstr .= '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
	$nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
	$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
	if ($custom != '')
		$nvpstr .= '&PAYMENTREQUEST_0_CUSTOM='.$custom;
	//uncomment this to disable echeck type payments and other delayable ones
	//$nvpstr .= "&PAYMENTREQUEST_0_ALLOWEDPAYMENTMETHOD=InstantPaymentOnly";
	$nvpstr .= '&L_BILLINGTYPE0='.urlencode('RecurringPayments');
	//$nvpstr .= "&L_BILLINGAGREEMENTDESCRIPTION0=" . urlencode($items[0]["name"]) . " Billed \${$repeat_amount} every {$period} Month(s)";
	$nvpstr .= '&L_BILLINGAGREEMENTDESCRIPTION0='.urlencode("Billed \${$repeat_amount} every {$period} Month(s).");
	if (count($items) > 1) {
		//$nvpstr .= "&PAYMENTREQUEST_1_AMT=". $items[sizeof($items) - 1]["amt"];
		//$nvpstr .= "&PAYMENTREQUEST_1_PAYMENTACTION=" . $paymentType;
		//$nvpstr .= "&PAYMENTREQUEST_1_CURRENCYCODE=" . $currencyCodeType;
		//$nvpstr .= "&L_BILLINGTYPE1=RecurringPayments";
		//$nvpstr .= "&L_BILLINGAGREEMENTDESCRIPTION1=" . urlencode($items[sizeof($items) - 1]["name"]) . (mb_strpos($items[sizeof($items) - 1]["name"], ' Domain ') !== false ? " Billed \${$items[sizeof($items) - 1]["amt"]} every 12 Month(s)" : "");
	}
	$agreement = 0;
	foreach($items as $index => $item) {
		//if (sizeof($items) > 1 && $index == (sizeof($items) - 1))
		//	$agreement++;
		$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_NAME{$index}=" . urlencode($item['name']);
		$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_AMT{$index}=" . urlencode($item['amt']);
		$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_QTY{$index}=" . urlencode($item['qty']);
		$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_ITEMCATEGORY{$index}=" . urlencode($category);
	}
	myadmin_log('billing', 'info', "got nvpstr {$nvpstr}", __LINE__, __FILE__);
	//' Make the API call to PayPal. If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment.  If an error occurred, show the resulting errors
	//myadmin_log('billing', 'info', "Making paypal_hash_call('SetExpressCheckout') with {$nvpstr}", __LINE__ , __FILE__);
	$resArray = paypal_hash_call('SetExpressCheckout', $nvpstr);
	myadmin_log('billing', 'info', 'SetSubscriptionExpressCheckout returned'.json_encode($resArray), __LINE__, __FILE__);
	$ack = mb_strtoupper($resArray['ACK']);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
		$token = $resArray['TOKEN'];
		$_SESSION['TOKEN'] = $token;
	}
	return $resArray;
}

/**
 * @param $token
 * @param $payer_id
 * @param $amt
 * @param $period
 * @param $description
 * @param bool $initamt
 * @return array
 */
function CreateRecurringPaymentsProfile($token, $payer_id, $amt, $period, $description, $initamt = false) {
	$str = '&TOKEN='.$token                                                // the token from  the SetExpressCheckout call also probably in $_SESSION['token']
		  .'&PAYERID='.$payer_id                                                // Identifies the customer's account
		  .'&BILLINGTYPE=RecurringPayments'                                        // This must be RecurringPayments for subscriptions, same as it was used in SetExpressCheckout
		  .'&AMT='.$amt                                                        // The amount the buyer will pay in a payment period
		   . ($initamt !== false ? '&INITAMT='.$initamt : '')                    // amount of initial payment , optional
		  .'&CURRENCYCODE='.'USD'                                                // The currency, e.g. US dollars
		  .'&COUNTRYCODE='.'US'                                                // The country code, e.g. US
		  .'&PROFILESTARTDATE='.urlencode(date("Y-m-d\TH:i:s\Z", time()))    // Billing date start, in UTC/GMT format
		  .'&BILLINGPERIOD='.'Month'                                            // Period of time between billings
		  .'&BILLINGFREQUENCY='.$period                                        // Frequency of charges
		  .'&DESC='.urlencode($description);									// Profile description - same as billing agreement description
	myadmin_log('billing', 'info', 'calling CreateRecurringPaymentsProfile '.$str, __LINE__, __FILE__);
	$resArray = paypal_hash_call('CreateRecurringPaymentsProfile', $str);
	myadmin_log('billing', 'info', 'CreateRecurringPaymentsProfile returned '.json_encode($resArray), __LINE__, __FILE__);
	return $resArray;
}

/**
 * Purpose:    Prepares the parameters for the SetExpressCheckout API Call for a Digital Goods payment.
 * Inputs:
 *        paymentAmount:    Total value of the shopping cart
 *        currencyCodeType:    Currency code value the PayPal API
 *        paymentType:        paymentType has to be one of the following values: Sale or Order or Authorization
 *        returnURL:            the page where buyers return to after they are done with the payment review on PayPal
 *        cancelURL:            the page where buyers return to when they cancel the payment review on PayPal
 *
 * @param $paymentAmount
 * @param $currencyCodeType
 * @param $paymentType
 * @param $returnURL
 * @param $cancelURL
 * @param $items
 * @return array
 */
function SetExpressCheckoutDG($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items) {
	// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
	$nvpstr = '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
	$nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
	$nvpstr .= '&RETURNURL='.$returnURL;
	$nvpstr .= '&CANCELURL='.$cancelURL;
	$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
	$nvpstr .= '&REQCONFIRMSHIPPING=0';
	$nvpstr .= '&NOSHIPPING=1';
	foreach($items as $index => $item) {
		$nvpstr .= "&L_PAYMENTREQUEST_0_NAME{$index}=" . urlencode($item['name']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_AMT{$index}=" . urlencode($item['amt']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_QTY{$index}=" . urlencode($item['qty']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY{$index}=Digital";
	}
	//' Make the API call to PayPal
	//' If the API call succeed, then redirect the buyer to PayPal to begin to authorize payment.
	//' If an error occurred, show the resulting errors
	$resArray = paypal_hash_call('SetExpressCheckout', $nvpstr);
	$ack = mb_strtoupper($resArray['ACK']);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
		$token = urldecode($resArray['TOKEN']);
		$_SESSION['TOKEN'] = $token;
	}
	return $resArray;
}

/**
 * @param $paymentAmount
 * @param $currencyCodeType
 * @param $paymentType
 * @param $returnURL
 * @param $cancelURL
 * @param $items
 * @param string $custom
 * @return array
 */
function SetExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $custom = '') {
	// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
	$nvpstr = '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
	$nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
	if ($custom != '')
		$nvpstr .= '&PAYMENTREQUEST_0_CUSTOM='.$custom;
	$nvpstr .= '&RETURNURL='.$returnURL;
	$nvpstr .= '&CANCELURL='.$cancelURL;
	$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
	$nvpstr .= '&REQCONFIRMSHIPPING=0';
	$nvpstr .= '&NOSHIPPING=1';
	foreach($items as $index => $item) {
		$nvpstr .= "&L_PAYMENTREQUEST_0_NAME{$index}=" . urlencode($item['name']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_AMT{$index}=" . urlencode($item['amt']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_QTY{$index}=" . urlencode($item['qty']);
		$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY{$index}=Physical";
	}
	//' Make the API call to PayPal
	//' If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment.
	//' If an error occurred, show the resulting errors
	$resArray = paypal_hash_call('SetExpressCheckout', $nvpstr);
	$ack = mb_strtoupper($resArray['ACK']);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
		$token = urldecode($resArray['TOKEN']);
		$_SESSION['TOKEN'] = $token;
	}

	return $resArray;
}

/* An express checkout transaction starts with a token, that
   identifies to PayPal your transaction
   In this example, when the script sees a token, the script
   knows that the buyer has already authorized payment through
   paypal.  If no token was found, the action is to send the buyer
   to PayPal to first authorize payment
   */

/*
' Purpose: 	Prepares the parameters for the SetExpressCheckout API Call.
' Inputs:
'		paymentAmount:  	Total value of the shopping cart
'		currencyCodeType: 	Currency code value the PayPal API
'		paymentType: 		paymentType has to be one of the following values: Sale or Order or Authorization
'		returnURL:			the page where buyers return to after they are done with the payment review on PayPal
'		cancelURL:			the page where buyers return to when they cancel the payment review on PayPal
*/

/**
 * @param $paymentAmount
 * @param $currencyCodeType
 * @param $paymentType
 * @param $returnURL
 * @param $cancelURL
 * @return array
 */
function CallShortcutExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL) {
	// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
	$nvpstr= '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
	$nvpstr = $nvpstr.'&RETURNURL='.$returnURL;
	$nvpstr = $nvpstr.'&CANCELURL='.$cancelURL;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
	$_SESSION['currencyCodeType'] = $currencyCodeType;
	$_SESSION['PaymentType'] = $paymentType;
	//' Make the API call to PayPal
	//' If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment.
	//' If an error occurred, show the resulting errors
	$resArray=paypal_hash_call('SetExpressCheckout', $nvpstr);
	$ack = mb_strtoupper($resArray['ACK']);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
		$token = urldecode($resArray['TOKEN']);
		$_SESSION['TOKEN']=$token;
	}
	return $resArray;
}

/**
 * ' Purpose:    Prepares the parameters for the SetExpressCheckout API Call.
 * ' Inputs:
 * '        paymentAmount:    Total value of the shopping cart
 * '        currencyCodeType:    Currency code value the PayPal API
 * '        paymentType:        paymentType has to be one of the following values: Sale or Order or Authorization
 * '        returnURL:            the page where buyers return to after they are done with the payment review on PayPal
 * '        cancelURL:            the page where buyers return to when they cancel the payment review on PayPal
 * '        shipToName:        the Ship to name entered on the merchant's site
 * '        shipToStreet:        the Ship to Street entered on the merchant's site
 * '        shipToCity:            the Ship to City entered on the merchant's site
 * '        shipToState:        the Ship to State entered on the merchant's site
 * '        shipToCountryCode:    the Code for Ship to Country entered on the merchant's site
 * '        shipToZip:            the Ship to ZipCode entered on the merchant's site
 * '        shipToStreet2:        the Ship to Street2 entered on the merchant's site
 * '        phoneNum:            the phoneNum  entered on the merchant's site
 *
 * @param $paymentAmount
 * @param $currencyCodeType
 * @param $paymentType
 * @param $returnURL
 * @param $cancelURL
 * @param $shipToName
 * @param $shipToStreet
 * @param $shipToCity
 * @param $shipToState
 * @param $shipToCountryCode
 * @param $shipToZip
 * @param $shipToStreet2
 * @param $phoneNum
 * @return array
 */
function CallMarkExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState, $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum) {
	// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
	$nvpstr= '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
	$nvpstr = $nvpstr.'&RETURNURL='.$returnURL;
	$nvpstr = $nvpstr.'&CANCELURL='.$cancelURL;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
	$nvpstr .= '&ADDROVERRIDE=1';
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTONAME='.$shipToName;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOSTREET='.$shipToStreet;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOSTREET2='.$shipToStreet2;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOCITY='.$shipToCity;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOSTATE='.$shipToState;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE='.$shipToCountryCode;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOZIP='.$shipToZip;
	$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_SHIPTOPHONENUM='.$phoneNum;
	$_SESSION['currencyCodeType'] = $currencyCodeType;
	$_SESSION['PaymentType'] = $paymentType;
	//' Make the API call to PayPal
	//' If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment.
	//' If an error occurred, show the resulting errors
	$resArray=paypal_hash_call('SetExpressCheckout', $nvpstr);
	$ack = mb_strtoupper($resArray['ACK']);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
		$token = urldecode($resArray['TOKEN']);
		$_SESSION['TOKEN']=$token;
	}
	return $resArray;
}

/**
 * Purpose:    Prepares the parameters for the GetExpressCheckoutDetails API Call.
 * Inputs:
 *        None
 * Returns:
 *        The NVP Collection object of the GetExpressCheckoutDetails Call Response.
 *        [TOKEN] => EC-7SC92877V2886181V
 *        [CHECKOUTSTATUS] => PaymentActionNotInitiated
 *        [TIMESTAMP] => 2012-08-30T03:25:31Z
 *        [CORRELATIONID] => 5f13f9904bd64
 *        [ACK] => Success
 *        [VERSION] => 84
 *        [BUILD] => 3587318
 *        [EMAIL] => detain_1346134361_per@corpmail.interserver.net
 *        [PAYERID] => 23YHDDSUSNB86
 *        [PAYERSTATUS] => verified
 *        [FIRSTNAME] => Joe
 *        [LASTNAME] => Huss
 *        [COUNTRYCODE] => US
 *        [CURRENCYCODE] => USD
 *        [AMT] => 6.00
 *        [ITEMAMT] => 6.00
 *        [SHIPPINGAMT] => 0.00
 *        [HANDLINGAMT] => 0.00
 *        [TAXAMT] => 0.00
 *        [INSURANCEAMT] => 0.00
 *        [SHIPDISCAMT] => 0.00
 *        [L_NAME0] => CentOS OpenVZ 1 Slice VPS
 *        [L_QTY0] => 1
 *        [L_TAXAMT0] => 0.00
 *        [L_AMT0] => 6.00
 *        [L_ITEMWEIGHTVALUE0] =>    0.00000
 *        [L_ITEMLENGTHVALUE0] =>    0.00000
 *        [L_ITEMWIDTHVALUE0] =>    0.00000
 *        [L_ITEMHEIGHTVALUE0] =>    0.00000
 *        [L_ITEMCATEGORY0] => Digital
 *        [PAYMENTREQUEST_0_CURRENCYCODE] => USD
 *        [PAYMENTREQUEST_0_AMT] => 6.00
 *        [PAYMENTREQUEST_0_ITEMAMT] => 6.00
 *        [PAYMENTREQUEST_0_SHIPPINGAMT] => 0.00
 *        [PAYMENTREQUEST_0_HANDLINGAMT] => 0.00
 *        [PAYMENTREQUEST_0_TAXAMT] => 0.00
 *        [PAYMENTREQUEST_0_INSURANCEAMT] => 0.00
 *        [PAYMENTREQUEST_0_SHIPDISCAMT] => 0.00
 *        [PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED] => false
 *        [L_PAYMENTREQUEST_0_NAME0] => CentOS OpenVZ 1 Slice VPS
 *        [L_PAYMENTREQUEST_0_QTY0] => 1
 *        [L_PAYMENTREQUEST_0_TAXAMT0] => 0.00
 *        [L_PAYMENTREQUEST_0_AMT0] => 6.00
 *        [L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0] =>    0.00000
 *        [L_PAYMENTREQUEST_0_ITEMLENGTHVALUE0] =>    0.00000
 *        [L_PAYMENTREQUEST_0_ITEMWIDTHVALUE0] =>    0.00000
 *        [L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE0] =>    0.00000
 *        [L_PAYMENTREQUEST_0_ITEMCATEGORY0] => Digital
 *        [PAYMENTREQUESTINFO_0_ERRORCODE] => 0
 *
 * @param $token
 * @return array|bool
 */
function GetExpressCheckoutDetails($token) {
	//' At this point, the buyer has completed authorizing the payment
	//' at PayPal.  The function will call PayPal to obtain the details
	//' of the authorization, including any shipping information of the
	//' buyer.  Remember, the authorization is not a completed transaction
	//' at this state - the buyer still needs an additional step to finalize
	//' the transaction
	//' Build a second API request to PayPal, using the token as the
	//'  ID to get the details on the payment authorization
	$nvpstr= '&TOKEN='.urlencode($token);
	//' Make the API call and store the results in an array.
	//'	If the call was a success, show the authorization details, and provide
	//' 	an action to complete the payment.
	//'	If failed, show the error
	$resArray=paypal_hash_call('GetExpressCheckoutDetails', $nvpstr);
	$ack = mb_strtoupper($resArray['ACK']);
	myadmin_log('billing', 'info', "GetExpressCheckoutDetails {$nvpstr}  returned " . json_encode($resArray), __LINE__, __FILE__);
	if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING')
		return $resArray;
	else
		return false;
}

/**
 * Purpose:    Prepares the parameters for the GetExpressCheckoutDetails API Call.
 * Inputs:
 *        sBNCode:    The BN code used by PayPal to track the transactions from a given shopping cart.
 * Returns:
 *        The NVP Collection object of the GetExpressCheckoutDetails Call Response.
 *        [TOKEN] => EC-7SC92877V2886181
 *        [SUCCESSPAGEREDIRECTREQUESTED] => false
 *        [TIMESTAMP] => 2012-08-30T03:25:36Z
 *        [CORRELATIONID] => c78c6f901c8ba
 *        [ACK] => Success
 *        [VERSION] => 84
 *        [BUILD] => 3587318
 *        [INSURANCEOPTIONSELECTED] => false
 *        [SHIPPINGOPTIONISDEFAULT] => false
 *        [PAYMENTINFO_0_TRANSACTIONID] => 99A86447T2674872E
 *        [PAYMENTINFO_0_TRANSACTIONTYPE] => expresscheckout
 *        [PAYMENTINFO_0_PAYMENTTYPE] => instant
 *        [PAYMENTINFO_0_ORDERTIME] => 2012-08-30T03:25:34Z
 *        [PAYMENTINFO_0_AMT] => 6.00
 *        [PAYMENTINFO_0_FEEAMT] => 0.47
 *        [PAYMENTINFO_0_TAXAMT] => 0.00
 *        [PAYMENTINFO_0_CURRENCYCODE] => USD
 *        [PAYMENTINFO_0_PAYMENTSTATUS] => Completed
 *        [PAYMENTINFO_0_PENDINGREASON] => None
 *        [PAYMENTINFO_0_REASONCODE] => None
 *        [PAYMENTINFO_0_PROTECTIONELIGIBILITY] => Ineligible
 *        [PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE] => None
 *        [PAYMENTINFO_0_SECUREMERCHANTACCOUNTID] => SBRBYA8FEJUSA
 *        [PAYMENTINFO_0_ERRORCODE] => 0
 *        [PAYMENTINFO_0_ACK] => Success
 *
 * @param $token
 * @param $paymentType
 * @param $currencyCodeType
 * @param $payerID
 * @param $FinalPaymentAmt
 * @return array
 */
function ConfirmPayment($token, $paymentType, $currencyCodeType, $payerID, $FinalPaymentAmt) {
	/* Gather the information to make the final call to finalize the PayPal payment.  The variable nvpstr holds the name value pairs */
	$nvpstr  = '&TOKEN='.urlencode($token)
	. '&PAYERID='.urlencode($payerID)
	. '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode($paymentType)
	. '&PAYMENTREQUEST_0_AMT='.urlencode($FinalPaymentAmt)
	. '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($currencyCodeType)
	. '&IPADDRESS='.urlencode($_SERVER['SERVER_NAME']);
	/* Make the call to PayPal to finalize payment If an error occurred, show the resulting errors */
	$resArray=paypal_hash_call('DoExpressCheckoutPayment', $nvpstr);
	myadmin_log('billing', 'info', "DoExpressCheckoutPayment {$nvpstr}  returned " . json_encode($resArray), __LINE__, __FILE__);
	/* Display the API response back to the browser. If the response from PayPal was a success, display the response parameters' If the response was an error, display the errors received using APIError.php. */
	$ack = mb_strtoupper($resArray['ACK']);
	return $resArray;
}

/**
 * ' Purpose:    This function makes a DoDirectPayment API call
 * '
 * ' Inputs:
 * '        paymentType:        paymentType has to be one of the following values: Sale or Order or Authorization
 * '        paymentAmount:    total value of the shopping cart
 * '        currencyCode:        currency code value the PayPal API
 * '        firstName:            first name as it appears on credit card
 * '        lastName:            last name as it appears on credit card
 * '        street:                buyer's street address line as it appears on credit card
 * '        city:                buyer's city
 * '        state:                buyer's state
 * '        countryCode:        buyer's country code
 * '        zip:                buyer's zip
 * '        creditCardType:        buyer's credit card type (i.e. Visa, MasterCard ... )
 * '        creditCardNumber:    buyers credit card number without any spaces, dashes or any other characters
 * '        expDate:            credit card expiration date
 * '        cvv2:                Card Verification Value
 * '
 * '
 * ' Returns:
 * '        The NVP Collection object of the DoDirectPayment Call Response.
 *
 * @param $paymentType
 * @param $paymentAmount
 * @param $creditCardType
 * @param $creditCardNumber
 * @param $expDate
 * @param $cvv2
 * @param $firstName
 * @param $lastName
 * @param $street
 * @param $city
 * @param $state
 * @param $zip
 * @param $countryCode
 * @param $currencyCode
 * @return array
 */
function DirectPayment($paymentType, $paymentAmount, $creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode, $currencyCode) {
	//Construct the parameter string that describes DoDirectPayment
	$nvpstr = '&AMT='.$paymentAmount;
	$nvpstr = $nvpstr.'&CURRENCYCODE='.$currencyCode;
	$nvpstr = $nvpstr.'&PAYMENTACTION='.$paymentType;
	$nvpstr = $nvpstr.'&CREDITCARDTYPE='.$creditCardType;
	$nvpstr = $nvpstr.'&ACCT='.$creditCardNumber;
	$nvpstr = $nvpstr.'&EXPDATE='.$expDate;
	$nvpstr = $nvpstr.'&CVV2='.$cvv2;
	$nvpstr = $nvpstr.'&FIRSTNAME='.$firstName;
	$nvpstr = $nvpstr.'&LASTNAME='.$lastName;
	$nvpstr = $nvpstr.'&STREET='.$street;
	$nvpstr = $nvpstr.'&CITY='.$city;
	$nvpstr = $nvpstr.'&STATE='.$state;
	$nvpstr = $nvpstr.'&COUNTRYCODE='.$countryCode;
	$nvpstr = $nvpstr.'&IPADDRESS='.$_SERVER['REMOTE_ADDR'];
	$resArray=paypal_hash_call('DoDirectPayment', $nvpstr);
	return $resArray;
}

/**
 * paypal_hash_call: Function to perform the API call to PayPal using API signature
 *
 * @methodName is name of API  method.
 * @nvpStr is nvp string.
 * returns an associative array containing the response from the server.
 * @param $methodName
 * @param $nvpStr
 * @return array
 */
function paypal_hash_call($methodName, $nvpStr) {
	//declaring of global variables
	global $API_Endpoint, $version, $API_UserName, $API_Password, $API_Signature;
	global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
	global $gv_ApiErrorURL;
	global $sBNCode;
	//setting the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	//curl_setopt($ch, CURLOPT_VERBOSE, 1);
	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
	if($USE_PROXY)
		curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST.':'.$PROXY_PORT);
	//NVPRequest for submitting to server
	$nvpreq= 'METHOD='.urlencode($methodName).'&VERSION='.urlencode($version).'&PWD='.urlencode($API_Password).'&USER='.urlencode($API_UserName).'&SIGNATURE='.urlencode($API_Signature) . $nvpStr.'&BUTTONSOURCE='.urlencode($sBNCode);
	//setting the nvpreq as POST FIELD to curl
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	//getting response from server
	$response = curl_exec($ch);
	//if (function_exists('myadmin_log'))
	//	myadmin_log('billing', 'info', "PayPal {$methodName} Call Got Curl Response {$response}", __LINE__, __FILE__);
	//converting NVPResponse to an Associative Array
	$nvpResArray=deformatNVP($response);
	$nvpReqArray=deformatNVP($nvpreq);
	$_SESSION['nvpReqArray']=$nvpReqArray;
	if (curl_errno($ch)) {
		// moving to display page to display curl errors
		$_SESSION['curl_error_no']=curl_errno($ch);
		$_SESSION['curl_error_msg']=curl_error($ch);
		//Execute the Error handling module to display errors.
	} else {
		//closing the curl
		curl_close($ch);
	}
	return $nvpResArray;
}

/**
 * Purpose: Redirects to PayPal.com site.
 * Inputs:  NVP string.
 * Returns:
 *
 * @param $token
 */
function RedirectToPayPal($token) {
	global $PAYPAL_URL;
	// Redirect to paypal.com here
	$payPalURL = $PAYPAL_URL . $token;
	header('Location: '.$payPalURL);
	exit;
}

/**
 * @param $token
 */
function RedirectToPayPalDG($token) {
	global $PAYPAL_DG_URL;
	// Redirect to paypal.com here
	$payPalURL = $PAYPAL_DG_URL . $token;
	header('Location: '.$payPalURL);
	exit;
}

/**
 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
 * It is useful to search for a particular key and displaying arrays.
 *
 * @nvpstr is NVPString.
 * @nvpArray is Associative Array.
 * @param $nvpstr
 * @return array
 */
function deformatNVP($nvpstr) {
	$intial = 0;
	$nvpArray = [];
	while(mb_strlen($nvpstr)) {
		//postion of Key
		$keypos= mb_strpos($nvpstr, '=');
		//position of value
		$valuepos = mb_strpos($nvpstr, '&') ? mb_strpos($nvpstr, '&'): mb_strlen($nvpstr);
		/*getting the Key and Value values and storing in a Associative Array*/
		$keyval=substr($nvpstr, $intial, $keypos);
		$valval=substr($nvpstr, $keypos+1, $valuepos-$keypos-1);
		//decoding the respose
		$nvpArray[urldecode($keyval)] =urldecode($valval);
		$nvpstr=substr($nvpstr, $valuepos+1, mb_strlen($nvpstr));
	}
	return $nvpArray;
}
