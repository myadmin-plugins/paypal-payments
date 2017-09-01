<?php

namespace Detain\MyAdminPaypal;

//include_once __DIR__.'/../../../../include/functions.inc.php';
include_once __DIR__.'/../../../../include/config/config.settings.php';

class PayPalCheckout {
	public static $sandboxFlag = FALSE;
	public static $sBNCode = 'PP-ECWizard';
	public static $sandboxApiEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';
	public static $sandboxPaypalUrl = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';
	public static $sandboxPaypalDgUrl = 'https://www.sandbox.paypal.com/incontext?token=';
	public static $liveApiEndpoint = 'https://api-3t.paypal.com/nvp';
	public static $livePaypalUrl = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
	public static $livePaypalDgUrl = 'https://www.paypal.com/incontext?token=';
	public static $proxyHost = '';
	public static $proxyPort = '';
	public static $useProxy = FALSE;
	public static $version = '109.0';

	public function __construct() {
	}

	public static function setSessionData($key, $value) {
		if (session_id() == '')
			session_start();
		$_SESSION[$key] = $value;
	}

	/**
	 * returns the proper API PayPal Digital Goods URL based on the sandboxFlag setting.
	 * @return string the API PayPal Digital Goods URL
	 */
	public static function getApiPaypalDgUrl() {
		return self::$sandboxFlag === TRUE ? self::$sandboxPaypalDgUrl: self::$livePaypalDgUrl;
	}

	/**
	 * returns the proper API PayPal URL based on the sandboxFlag setting.
	 * @return string the API PayPal URL
	 */
	public static function getApiPaypalUrl() {
		return self::$sandboxFlag === TRUE ? self::$sandboxPaypalUrl: self::$livePaypalUrl;
	}

	/**
	 * returns the proper API Endpoint based on the sandboxFlag setting.
	 * @return string the API Endpoint
	 */
	public static function getApiEndpoint() {
		return self::$sandboxFlag === TRUE ? self::$sandboxApiEndpoint : self::$liveApiEndpoint;
	}

	/**
	 * returns the proper API Username based on the sandboxFlag setting.
	 * @return string the API Username
	 */
	public static function getApiUsername() {
		return self::$sandboxFlag === TRUE ? PAYPAL_SANDBOX_API_USERNAME : PAYPAL_API_USERNAME;
	}

	/**
	 * returns the proper API Password based on the sandboxFlag setting.
	 * @return string the API Password
	 */
	public static function getApiPassword() {
		return self::$sandboxFlag === TRUE ? PAYPAL_SANDBOX_API_PASSWORD : PAYPAL_API_PASSWORD;
	}

	/**
	 * returns the proper API Signature based on the sandboxFlag setting.
	 * @return string the API Signature
	 */
	public static function getApiSignature() {
		return self::$sandboxFlag === TRUE ? PAYPAL_SANDBOX_API_SIGNATURE : PAYPAL_API_SIGNATURE;
	}

	/**
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $returnURL the page where buyers return to after they are done with the payment review on PayPal
	 * @param string $cancelURL the page where buyers return to when they cancel the payment review on PayPal
	 * @param array $items array of items being purchased
	 * @param int $period
	 * @param bool $repeat_amount
	 * @param string $category
	 * @param string $custom
	 * @return array
	 */
	public static function SetSubscriptionExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $period = 1, $repeat_amount = FALSE, $category = 'Physical', $custom = '') {
		if ($repeat_amount == FALSE)
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
			//$nvpstr .= "&L_BILLINGAGREEMENTDESCRIPTION1=" . urlencode($items[sizeof($items) - 1]["name"]) . (mb_strpos($items[sizeof($items) - 1]["name"], ' Domain ') !== FALSE ? " Billed \${$items[sizeof($items) - 1]["amt"]} every 12 Month(s)" : "");
		}
		$agreement = 0;
		foreach ($items as $index => $item) {
			//if (sizeof($items) > 1 && $index == (sizeof($items) - 1))
			//	$agreement++;
			$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_NAME{$index}=" . urlencode($item['name']);
			$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_AMT{$index}=" . urlencode($item['amt']);
			$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_QTY{$index}=" . urlencode($item['qty']);
			$nvpstr .= "&L_PAYMENTREQUEST_{$agreement}_ITEMCATEGORY{$index}=" . urlencode($category);
		}
		myadmin_log('billing', 'info', "got nvpstr {$nvpstr}", __LINE__, __FILE__);
		//' Make the API call to PayPal. If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment.  If an error occurred, show the resulting errors
		//myadmin_log('billing', 'info', "Making paypal_hash_call('SetExpressCheckout') with {$nvpstr}", __LINE__, __FILE__);
		$resArray = self::paypal_hash_call('SetExpressCheckout', $nvpstr);
		myadmin_log('billing', 'info', 'SetSubscriptionExpressCheckout returned'.json_encode($resArray), __LINE__, __FILE__);
		$ack = mb_strtoupper($resArray['ACK']);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$token = $resArray['TOKEN'];
			self::setSessionData('TOKEN', $token);
		}
		return $resArray;
	}

	/**
	 * @param string $token the token from  the SetExpressCheckout call also probably in $_SESSION['token']
	 * @param string $payer_id Identifies the customer's account
	 * @param float|string $amt The amount the buyer will pay in a payment period
	 * @param int|string $period Frequency of charges
	 * @param string $description Profile description - same as billing agreement description
	 * @param bool|float|string $initamt optional amount of initial payment
	 * @return array
	 */
	public static function CreateRecurringPaymentsProfile($token, $payer_id, $amt, $period, $description, $initamt = FALSE) {
		$str = '&TOKEN='.$token
			.'&PAYERID='.$payer_id
			.'&BILLINGTYPE=RecurringPayments'                                  // This must be RecurringPayments for subscriptions, same as it was used in SetExpressCheckout
			.'&AMT='.$amt
			. ($initamt !== FALSE ? '&INITAMT='.$initamt : '')
			.'&CURRENCYCODE='.'USD'                                            // The currency, e.g. US dollars
			.'&COUNTRYCODE='.'US'                                              // The country code, e.g. US
			.'&PROFILESTARTDATE='.urlencode(date("Y-m-d\TH:i:s\Z", time()))    // Billing date start, in UTC/GMT format
			.'&BILLINGPERIOD='.'Month'                                         // Period of time between billings
			.'&BILLINGFREQUENCY='.$period
			.'&DESC='.urlencode($description);
		myadmin_log('billing', 'info', 'calling CreateRecurringPaymentsProfile '.$str, __LINE__, __FILE__);
		$resArray = self::paypal_hash_call('CreateRecurringPaymentsProfile', $str);
		myadmin_log('billing', 'info', 'CreateRecurringPaymentsProfile returned '.json_encode($resArray), __LINE__, __FILE__);
		return $resArray;
	}

	/**
	 * Prepares the parameters for the SetExpressCheckout API Call for a Digital Goods payment.
	 *
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $returnURL the page where buyers return to after they are done with the payment review on PayPal
	 * @param string $cancelURL the page where buyers return to when they cancel the payment review on PayPal
	 * @param array $items array of items being purchased
	 * @return array the response array
	 */
	public static function SetExpressCheckoutDG($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items) {
		// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
		$nvpstr = '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
		$nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
		$nvpstr .= '&RETURNURL='.$returnURL;
		$nvpstr .= '&CANCELURL='.$cancelURL;
		$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
		$nvpstr .= '&REQCONFIRMSHIPPING=0';
		$nvpstr .= '&NOSHIPPING=1';
		foreach ($items as $index => $item) {
			$nvpstr .= "&L_PAYMENTREQUEST_0_NAME{$index}=" . urlencode($item['name']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_AMT{$index}=" . urlencode($item['amt']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_QTY{$index}=" . urlencode($item['qty']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY{$index}=Digital";
		}
		//' Make the API call to PayPal
		//' If the API call succeed, then redirect the buyer to PayPal to begin to authorize payment.
		//' If an error occurred, show the resulting errors
		$resArray = self::paypal_hash_call('SetExpressCheckout', $nvpstr);
		$ack = mb_strtoupper($resArray['ACK']);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$token = urldecode($resArray['TOKEN']);
			self::setSessionData('TOKEN', $token);
		}
		return $resArray;
	}

	/**
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $returnURL the page where buyers return to after they are done with the payment review on PayPal
	 * @param string $cancelURL the page where buyers return to when they cancel the payment review on PayPal
	 * @param $items
	 * @param string $custom
	 * @return array
	 */
	public static function SetExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $custom = '') {
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
		foreach ($items as $index => $item) {
			$nvpstr .= "&L_PAYMENTREQUEST_0_NAME{$index}=" . urlencode($item['name']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_AMT{$index}=" . urlencode($item['amt']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_QTY{$index}=" . urlencode($item['qty']);
			$nvpstr .= "&L_PAYMENTREQUEST_0_ITEMCATEGORY{$index}=Physical";
		}
		// Make the API call to PayPal. If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment. If an error occurred, show the resulting errors
		$resArray = self::paypal_hash_call('SetExpressCheckout', $nvpstr);
		$ack = mb_strtoupper($resArray['ACK']);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$token = urldecode($resArray['TOKEN']);
			self::setSessionData('TOKEN', $token);
		}
		return $resArray;
	}

	/**
	 * Prepares the parameters for the SetExpressCheckout API Call.
	 *
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $returnURL the page where buyers return to after they are done with the payment review on PayPal
	 * @param string $cancelURL the page where buyers return to when they cancel the payment review on PayPal
	 * @return array response array
	 */
	public static function CallShortcutExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL) {
		// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
		$nvpstr = '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
		$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_PAYMENTACTION='.$paymentType;
		$nvpstr = $nvpstr.'&RETURNURL='.$returnURL;
		$nvpstr = $nvpstr.'&CANCELURL='.$cancelURL;
		$nvpstr = $nvpstr.'&PAYMENTREQUEST_0_CURRENCYCODE='.$currencyCodeType;
		self::setSessionData('currencyCodeType', $currencyCodeType);
		self::setSessionData('PaymentType', $paymentType);
		// Make the API call to PayPal. If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment. If an error occurred, show the resulting errors
		$resArray = self::paypal_hash_call('SetExpressCheckout', $nvpstr);
		$ack = mb_strtoupper($resArray['ACK']);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$token = urldecode($resArray['TOKEN']);
			self::setSessionData('TOKEN', $token);
		}
		return $resArray;
	}

	/**
	 * Prepares the parameters for the SetExpressCheckout API Call.
	 *
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $returnURL the page where buyers return to after they are done with the payment review on PayPal
	 * @param string $cancelURL the page where buyers return to when they cancel the payment review on PayPal
	 * @param string $shipToName the Ship to name entered on the merchant's site
	 * @param string $shipToStreet the Ship to Street entered on the merchant's site
	 * @param string $shipToCity the Ship to City entered on the merchant's site
	 * @param string $shipToState the Ship to State entered on the merchant's site
	 * @param string $shipToCountryCode the Code for Ship to Country entered on the merchant's site
	 * @param string $shipToZip the Ship to ZipCode entered on the merchant's site
	 * @param string $shipToStreet2 the Ship to Street2 entered on the merchant's site
	 * @param string $phoneNum the phoneNum  entered on the merchant's site
	 * @return array the response array
	 */
	public static function CallMarkExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState, $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum) {
		// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
		$nvpstr = '&PAYMENTREQUEST_0_AMT='.$paymentAmount;
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
		self::setSessionData('currencyCodeType', $currencyCodeType);
		self::setSessionData('PaymentType', $paymentType);
		// Make the API call to PayPal. If the API call succeeded, then redirect the buyer to PayPal to begin to authorize payment. If an error occurred, show the resulting errors
		$resArray = self::paypal_hash_call('SetExpressCheckout', $nvpstr);
		$ack = mb_strtoupper($resArray['ACK']);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$token = urldecode($resArray['TOKEN']);
			self::setSessionData('TOKEN', $token);
		}
		return $resArray;
	}

	/**
	 * Prepares the parameters for the GetExpressCheckoutDetails API Call.
	 *
	 * @param string $token
	 * @return array|bool false on error or The NVP Collection object of the GetExpressCheckoutDetails Call Response.
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
	 *        [PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED] => FALSE
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
	 */
	public static function GetExpressCheckoutDetails($token) {
		// At this point, the buyer has completed authorizing the payment at PayPal.  The function will call PayPal to obtain the details of the authorization, including any shipping information of the
		// buyer.  Remember, the authorization is not a completed transaction at this state - the buyer still needs an additional step to finalize the transaction
		// Build a second API request to PayPal, using the token as the ID to get the details on the payment authorization
		$nvpstr= '&TOKEN='.urlencode($token);
		// Make the API call and store the results in an array. If the call was a success, show the authorization details, and provide an action to complete the payment. If failed, show the error
		$resArray = self::paypal_hash_call('GetExpressCheckoutDetails', $nvpstr);
		$ack = mb_strtoupper($resArray['ACK']);
		myadmin_log('billing', 'info', "GetExpressCheckoutDetails {$nvpstr}  returned " . json_encode($resArray), __LINE__, __FILE__);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING')
			return $resArray;
		else
			return FALSE;
	}

	/**
	 * Prepares the parameters for the GetExpressCheckoutDetails API Call.
	 * Inputs:
	 *        sBNCode:    The Buy Now code used by PayPal to track the transactions from a given shopping cart.
	 * @param string $token
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param string $currencyCodeType Currency code value the PayPal API
	 * @param string $payerID
	 * @param float|string $FinalPaymentAmt
	 * @return array The NVP Collection object of the GetExpressCheckoutDetails Call Response.
	 *        [TOKEN] => EC-7SC92877V2886181
	 *        [SUCCESSPAGEREDIRECTREQUESTED] => FALSE
	 *        [TIMESTAMP] => 2012-08-30T03:25:36Z
	 *        [CORRELATIONID] => c78c6f901c8ba
	 *        [ACK] => Success
	 *        [VERSION] => 84
	 *        [BUILD] => 3587318
	 *        [INSURANCEOPTIONSELECTED] => FALSE
	 *        [SHIPPINGOPTIONISDEFAULT] => FALSE
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
	 */
	public static function ConfirmPayment($token, $paymentType, $currencyCodeType, $payerID, $FinalPaymentAmt) {
		/* Gather the information to make the final call to finalize the PayPal payment.  The variable nvpstr holds the name value pairs */
		$nvpstr  = '&TOKEN='.urlencode($token)
		. '&PAYERID='.urlencode($payerID)
		. '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode($paymentType)
		. '&PAYMENTREQUEST_0_AMT='.urlencode($FinalPaymentAmt)
		. '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($currencyCodeType)
		. '&IPADDRESS='.urlencode($_SERVER['SERVER_NAME']);
		/* Make the call to PayPal to finalize payment If an error occurred, show the resulting errors */
		$resArray = self::paypal_hash_call('DoExpressCheckoutPayment', $nvpstr);
		myadmin_log('billing', 'info', "DoExpressCheckoutPayment {$nvpstr}  returned " . json_encode($resArray), __LINE__, __FILE__);
		/* Display the API response back to the browser. If the response from PayPal was a success, display the response parameters' If the response was an error, display the errors received using APIError.php. */
		$ack = mb_strtoupper($resArray['ACK']);
		return $resArray;
	}

	/**
	 * This function makes a DoDirectPayment API call
	 *
	 * @param string $paymentType has to be one of the following values: Sale or Order or Authorization
	 * @param float|string $paymentAmount Total value of the shopping cart
	 * @param string $creditCardType buyer's credit card type (i.e. Visa, MasterCard ... )
	 * @param string $creditCardNumber buyers credit card number without any spaces, dashes or any other characters
	 * @param string $expDate credit card expiration date
	 * @param string $cvv2 Card Verification Value
	 * @param string $firstName first name as it appears on credit card
	 * @param string $lastName last name as it appears on credit card
	 * @param string $street buyer's street address line as it appears on credit card
	 * @param string $city buyer's city
	 * @param string $state buyer's state
	 * @param string $zip buyer's zip
	 * @param string $countryCode buyer's country code
	 * @param string $currencyCode currency code value the PayPal API
	 * @return array The NVP Collection object of the DoDirectPayment Call Response.
	 */
	public static function DirectPayment($paymentType, $paymentAmount, $creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode, $currencyCode) {
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
		$resArray = self::paypal_hash_call('DoDirectPayment', $nvpstr);
		return $resArray;
	}

	/**
	 * perform the API call to PayPal using API signature
	 *
	 * @param string $methodName name of API  method.
	 * @param string $nvpStr nvp string.
	 * @return array returns an associative array containing the response from the server.
	 */
	public static function paypal_hash_call($methodName, $nvpStr) {
		//setting the curl parameters.
		$ch = curl_init();
		//myadmin_log('paypal', 'debug', self::getApiEndpoint(), __LINE__, __FILE__);
		curl_setopt($ch, CURLOPT_URL, self::getApiEndpoint());
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
		if (self::$useProxy === TRUE)
			curl_setopt($ch, CURLOPT_PROXY, self::$proxyHost.':'.self::$proxyPort);
		//NVPRequest for submitting to server
		$nvpreq= 'METHOD='.urlencode($methodName).'&VERSION='.urlencode(self::$version).'&PWD='.urlencode(self::getApiPassword()).'&USER='.urlencode(self::getApiUsername()).'&SIGNATURE='.urlencode(self::getApiSignature()) . $nvpStr.'&BUTTONSOURCE='.urlencode(self::$sBNCode);
		//myadmin_log('paypal', 'debug', $nvpreq, __LINE__, __FILE__);
		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		//getting response from server
		$response = curl_exec($ch);
		//if (function_exists('myadmin_log'))
			//myadmin_log('billing', 'info', "PayPal {$methodName} Call Got Curl Response {$response}", __LINE__, __FILE__);
		//converting NVPResponse to an Associative Array
		$nvpResArray = self::deformatNVP($response);
		$nvpReqArray = self::deformatNVP($nvpreq);
		self::setSessionData('nvpReqArray', $nvpReqArray);
		if (curl_errno($ch)) {
			// moving to display page to display curl errors
			self::setSessionData('curl_error_no', curl_errno($ch));
			self::setSessionData('curl_error_msg', curl_error($ch));
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
	public static function RedirectToPayPal($token) {
		// Redirect to paypal.com here
		$payPalURL = self::getApiPaypalUrl() . $token;
		header('Location: '.$payPalURL);
		exit;
	}

	/**
	 * @param $token
	 */
	public static function RedirectToPayPalDG($token) {
		// Redirect to paypal.com here
		$payPalURL = self::getApiPaypalDgUrl() . $token;
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
	public static function deformatNVP($nvpstr) {
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
}
