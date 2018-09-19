<?php
	/**
	 * PayPal Related Functionality
	 * @author Joe Huss <detain@interserver.net>
	 * @copyright 2018
	 * @package MyAdmin
	 * @category PayPal
	 */

	function get_paypal_transaction_types()
	{
		return [
			'-' => 'Credit card chargeback if the case_type variable contains chargeback',
			'adjustment' => 'A dispute has been resolved and closed',
			'cart' => 'Payment received for multiple items; source is Express Checkout or the PayPal Shopping Cart.',
			'express_checkout' => 'Payment received for a single item; source is Express Checkout',
			'masspay' => 'Payment sent using Mass Pay',
			'merch_pmt' => 'Monthly subscription paid for Website Payments Pro, Reference transactions, or Billing Agreement payments',
			'mp_cancel' => 'Billing agreement cancelled',
			'mp_signup' => 'Created a billing agreement',
			'new_case' => 'A new dispute was filed',
			'payout' => 'A payout related to a global shipping transaction was completed.',
			'pro_hosted' => 'Payment received; source is Website Payments Pro Hosted Solution.',
			'recurring_payment' => 'Recurring payment received',
			'recurring_payment_expired' => 'Recurring payment expired',
			'recurring_payment_failed' => "Recurring payment failed \nThis transaction type is sent if:\n\nThe attempt to collect a recurring payment fails\nThe \"max failed payments\" setting in the customer's recurring payment profile is 0 \nIn this case, PayPal tries to collect the recurring payment an unlimited number of times without ever suspending the customer's recurring payments profile.",
			'recurring_payment_profile_cancel' => 'Recurring payment profile canceled',
			'recurring_payment_profile_created' => 'Recurring payment profile created',
			'recurring_payment_skipped' => 'Recurring payment skipped; it will be retried up to 3 times, 5 days apart',
			'recurring_payment_suspended' => "Recurring payment suspended\nThis transaction type is sent if PayPal tried to collect a recurring payment, but the related recurring payments profile has been suspended.",
			'recurring_payment_suspended_due_to_max_failed_payment' => "Recurring payment failed and the related recurring payment profile has been suspended\nThis transaction type is sent if:\n\nPayPal's attempt to collect a recurring payment failed \nThe \"max failed payments\" setting in the customer's recurring payment profile is 1 or greater \nthe number of attempts to collect payment has exceeded the value specified for \"max failed payments\"\nIn this case, PayPal suspends the customer's recurring payment profile.",
			'send_money' => 'Payment received; source is the Send Money tab on the PayPal website',
			'subscr_cancel' => 'Subscription canceled',
			'subscr_eot' => 'Subscription expired',
			'subscr_failed' => 'Subscription payment failed',
			'subscr_modify' => 'Subscription modified',
			'subscr_payment' => 'Subscription payment received',
			'subscr_signup' => 'Subscription started',
			'virtual_terminal' => 'Payment received; source is Virtual Terminal',
			'web_accept' => "Payment received; source is any of the following:\n\nA Direct Credit Card (Pro) transaction\nA Buy Now, Donation or Smart Logo for eBay auctions button"
		];
	}

/**
 * @return array
 */
function get_paypal_cats_and_fields()
{
	return [
			[
				'name' => 'Transaction and Notification Information',
				'desc' => 'Transaction and notification-related variables identify the merchant that is receiving a payment or other notification and transaction-specific information.',
				'fields' => [
					'business' => "Email address or account ID of the payment\nrecipient (that is, the merchant). Equivalent to the values of receiver_email (if\npayment is sent to primary account) and business set\nin the Website Payment HTML.\n\nNote: The value of this variable\nis normalized to lowercase characters. \nLength: 127\ncharacters",
					'charset' => 'Character set',
					'custom' => "Custom value as passed by you, the merchant.\nThese are pass-through variables that are never presented to your\ncustomerLength: 255 characters",
					'ipn_track_id' => 'Internal; only for use by MTS and DTS',
					'notify_version' => "Message's version number",
					'parent_txn_id' => "In the case of a refund, reversal, or canceled\nreversal, this variable contains the txn_id of\nthe original transaction, while txn_id contains\na new ID for the new transaction.Length: 19 characters",
					'receipt_id' => "Unique ID generated during guest checkout\n(payment by credit card without logging in).",
					'receiver_email' => "Primary email address of the payment recipient\n(that is, the merchant). If the payment is sent to a non-primary\nemail address on your PayPal account, the receiver_email is\nstill your primary email.\n\nNote: The value of this variable\nis normalized to lowercase characters. \n\nLength: 127\ncharacters",
					'receiver_id' => "Unique account ID of the payment recipient\n(i.e., the merchant). This is the same as the recipient's referral\nID.Length: 13 characters",
					'resend' => "Whether this IPN message was resent (equals true);\notherwise, this is the original message.",
					'residence_country' => "ISO 3166 country code associated with the\ncountry of residenceLength: 2 characters",
					'test_ipn' => "Whether the message is a test message. It\nis one of the following values:\n\n\n1 -\nthe message is directed to the Sandbox",
					'txn_id' => "The merchant's original transaction identification\nnumber for the payment from the buyer, against which the case was\nregistered.",
					'txn_type' => "The kind of transaction for which the IPN\nmessage was sent.",
					'verify_sign' => "Encrypted string used to validate the authenticity\nof the transaction"
				]
			],
			[
				'name' => 'Buyer Information',
				'desc' => 'Buyer information identifies the buyer or initiator of a transaction by payer ID or email address. Additional contact or shipping information may be provided.',
				'fields' => [
					'address_country' => "Country of customer's addressLength:\n64 characters",
					'address_city' => "City of customer's addressLength:\n40 characters",
					'address_country_code' => "ISO 3166 country code associated with customer's\naddressLength: 2 characters",
					'address_name' => "Name used with address (included when the\ncustomer provides a Gift Address)Length: 128 characters",
					'address_state' => "State of customer's addressLength:\n40 characters",
					'address_status' => "Whether the customer provided a confirmed\naddress. It is one of the following values:\n\n\nconfirmed -\nCustomer provided a confirmed address.\n\n\nunconfirmed - Customer provided an unconfirmed\naddress.",
					'address_street' => "Customer's street address.Length:\n200 characters",
					'address_zip' => "Zip code of customer's address.Length:\n20 characters",
					'contact_phone' => "Customer's telephone number.Length:\n20 characters",
					'first_name' => "Customer's first nameLength: 64 characters",
					'last_name' => "Customer's last nameLength: 64 characters",
					'payer_business_name' => "Customer's company name, if customer is\na businessLength: 127 characters",
					'payer_email' => "Customer's primary email address. Use this\nemail to provide any credits.Length: 127 characters",
					'payer_id' => 'Unique customer ID.Length: 13 characters'
				]
			],
			[
				'name' => 'Payment Information',
				'desc' => 'Payment information identifies the amount and status of a payment transaction, including fees.',
				'fields' => [
					'auth_amount' => 'Authorization amount',
					'auth_exp' => "Authorization expiration date and time,\nin the following format: HH:MM:SS DD Mmm YY, YYYY PSTLength:\n28 characters",
					'auth_id' => "Authorization identification numberLength:\n19 characters",
					'auth_status' => 'Status of authorization',
					'echeck_time_processed' => "The time an eCheck was processed; for example,\nwhen the status changes to Success or Completed. The format is as\nfollows: hh:mm:ss MM DD, YYYY ZONE, e.g. 04:55:30 May 26, 2011 PDT.",
					'exchange_rate' => "Exchange rate used if a currency conversion\noccurred.",
					'fraud_management_pending_filters_x' => "One or more filters that identify a triggering\naction associated with one of the following payment_status values:\nPending, Completed, Denied, where x is a number\nstarting with 1 that makes the IPN variable name unique; x is\nnot the filter's ID number. The filters and their ID numbers are\nas follows:\n\n\n1 - AVS No Match\n\n\n2 - AVS Partial Match\n\n\n3 - AVS Unavailable/Unsupported\n\n\n4 - Card Security Code (CSC) Mismatch\n\n\n5 - Maximum Transaction Amount\n\n\n6 - Unconfirmed Address\n\n\n7 - Country Monitor\n\n\n8 - Large Order Number\n\n\n9 - Billing/Shipping Address Mismatch\n\n\n10 - Risky ZIP Code\n\n\n11 - Suspected Freight Forwarder Check\n\n\n12 - Total Purchase Price Minimum\n\n\n13 - IP Address Velocity\n\n\n14 - Risky Email Address Domain Check\n\n\n15 - Risky Bank Identification Number (BIN)\nCheck\n\n\n16 - Risky IP Address Range\n\n\n17 - PayPal Fraud Model",
					'invoice' => "Pass-through variable you can use to identify\nyour Invoice Number for this purchase. If omitted, no variable is\npassed back.Length: 127 characters",
					'item_namex' => "Item name as passed by you, the merchant.\nOr, if not passed by you, as entered by your customer. If this is\na shopping cart transaction, PayPal will append the number of the\nitem (e.g., item_name1, item_name2, and\nso forth).Length: 127 characters",
					'item_numberx' => "Pass-through variable for you to track purchases.\nIt will get passed back to you at the completion of the payment.\nIf omitted, no variable will be passed back to you. If this is a\nshopping cart transaction, PayPal will append the number of the\nitem (e.g., item_number1, item_number2, and\nso forth)Length: 127 characters",
					'mc_currency' => "For payment IPN notifications, this\nis the currency of the payment. \n\nFor non-payment subscription IPN notifications (i.e., txn_type= signup,\ncancel, failed, eot, or modify), this is the currency of the subscription. \n\nFor payment subscription IPN notifications, it is the currency\nof the payment (i.e., txn_type = subscr_payment)",
					'mc_fee' => "Transaction fee associated with the payment. mc_gross minus mc_fee equals\nthe amount deposited into the receiver_email account. Equivalent\nto payment_fee for USD payments. If this amount\nis negative, it signifies a refund or reversal, and either of those\npayment statuses can be for the full or partial amount of the original\ntransaction fee.",
					'mc_gross' => "Full amount of the customer's payment, before\ntransaction fee is subtracted. Equivalent to payment_gross for\nUSD payments. If this amount is negative, it signifies a refund\nor reversal, and either of those payment statuses can be for the\nfull or partial amount of the original transaction.",
					'mc_gross_x' => "The amount is in the currency of mc_currency,\nwhere x is the shopping cart detail item number.\nThe sum of mc_gross_x should\ntotal mc_gross.",
					'mc_handling' => "Total handling amount associated with the\ntransaction.",
					'mc_shipping' => "Total shipping amount associated with the\ntransaction.",
					'mc_shippingx' => "This is the combined total of shipping1 and shipping2 Website Payments\nStandard variables, where x is the shopping cart\ndetail item number. The shippingx variable\nis only shown when the merchant applies a shipping amount for a\nspecific item. Because profile shipping might apply, the sum of shippingx might\nnot be equal to shipping.",
					'memo' => "Memo as entered by your customer in PayPal\nWebsite Payments note field.Length: 255 characters",
					'num_cart_items' => "If this is a PayPal Shopping Cart transaction,\nnumber of items in cart.",
					'option_name1' => "Option 1 name as requested by you. PayPal\nappends the number of the item where x represents\nthe number of the shopping cart detail item (e.g., option_name1, option_name2).Length:\n64 characters",
					'option_name2' => "Option 2 name as requested by you. PayPal\nappends the number of the item where x represents\nthe number of the shopping cart detail item (e.g., option_name2, option_name2).Length:\n64 characters",
					'option_selection1' => "Option 1 choice as entered by your customer.PayPal\nappends the number of the item where x represents\nthe number of the shopping cart detail item (e.g., option_selection1, option_selection2).Length:\n200 characters",
					'option_selection2' => "Option 2 choice as entered by your customer.PayPal\nappends the number of the item where x represents\nthe number of the shopping cart detail item (e.g., option_selection1, option_selection2).Length:\n200 characters",
					'payer_status' => "Whether the customer has a verified PayPal\naccount.\n\n\nverified - Customer has\na verified PayPal account.\n\n\nunverified - Customer has an unverified\nPayPal account.",
					'payment_date' => "Time/Date stamp generated by PayPal, in\nthe following format: HH:MM:SS Mmm DD, YYYY PDTLength: 28\ncharacters",
					'payment_fee' => "USD transaction fee associated with the\npayment. payment_gross minus payment_fee equals\nthe amount deposited into the receiver email account. Is empty for\nnon-USD payments. If this amount is negative, it signifies a refund\nor reversal, and either of those payment statuses can be for the\nfull or partial amount of the original transaction fee.\n\nNote: This\nis a deprecated field. Use mc_fee instead.",
					'payment_fee_x' => "If the payment is USD, then the value is\nthe same as that for mc_fee_x, where x is\nthe record number; if the currency is not USD, then this is an empty\nstring.\n\nNote: This is a deprecated field. Use mc_fee_x instead.",
					'payment_gross' => "Full USD amount of the customer's payment,\nbefore transaction fee is subtracted. Will be empty for non-USD\npayments. This is a legacy field replaced by mc_gross.\nIf this amount is negative, it signifies a refund or reversal, and\neither of those payment statuses can be for the full or partial amount\nof the original transaction.",
					'payment_gross_x' => "If the payment is USD, then the value for\nthis is the same as that for the mc_gross_x,\nwhere x is the record number the mass pay item.\nIf the currency is not USD, this is an empty string.\n\nNote: This\nis a deprecated field. Use mc_gross_x instead.",
					'payment_status' => "The\nstatus of the payment:\nCanceled_Reversal:\nA reversal has been canceled. For example, you won a dispute with\nthe customer, and the funds for the transaction that was reversed\nhave been returned to you.\nCompleted: The payment\nhas been completed, and the funds have been added successfully to\nyour account balance.\nCreated: A German ELV\npayment is made using Express Checkout.\nDenied: The payment was denied. This happens only if the payment was previously pending\nbecause of one of the reasons listed for the pending_reason variable\nor the Fraud_Management_Filters_x variable.\nExpired:\nThis authorization has expired and cannot be captured.\nFailed:\nThe payment has failed. This happens only if the payment was made\nfrom your customer's bank account.\nPending: The payment\nis pending. See pending_reason for\nmore information.\nRefunded: You refunded\nthe payment.\nReversed: A payment\nwas reversed due to a chargeback or other type of reversal. The\nfunds have been removed from your account balance and returned to\nthe buyer. The reason for the reversal is specified in the ReasonCode element.\nProcessed:\nA payment has been accepted.\nVoided: This authorization\nhas been voided.",
					'payment_type' => "echeck: This payment was\nfunded with an eCheck.instant: This payment\nwas funded with PayPal balance, credit card, or Instant Transfer.",
					'pending_reason' => "This variable is set only if payment_status is  Pending.\naddress:\nThe payment is pending because your customer did not include a confirmed\nshipping address and your Payment Receiving Preferences is set yo\nallow you to manually accept or deny each of these payments. To change\nyour preference, go to the Preferences section\nof your Profile.\nauthorization:\nYou set the payment action to Authorization and have not yet captured\nfunds.\necheck: The payment\nis pending because it was made by an eCheck that has not yet cleared.\nintl:\nThe payment is pending because you hold a non-U.S. account and do\nnot have a withdrawal mechanism. You must manually accept or deny this\npayment from your Account\nOverview.\nmulti_currency: You do not have a balance in the currency sent, and you do not have your profiles's Payment Receiving Preferences option set to automatically convert and accept this payment. As a result, you must manually accept or deny this payment.\norder:\nYou set the payment action to Order and have not yet captured funds.\npaymentreview:\nThe payment is pending while it is  reviewed by PayPal for\nrisk.\nregulatory_review:  The payment is pending because PayPal is reviewing it for  compliance with government regulations. PayPal will complete this review within 72 hours. When the review is complete, you will receive a second IPN message whose payment_status/reason code variables  indicate the result.\nunilateral: The payment\nis pending because it was made to an email address that is not yet\nregistered or confirmed.\nupgrade: The payment\nis pending because it was made via credit card and you must upgrade\nyour account to Business or Premier status before you can receive the\nfunds. upgrade can\nalso mean that you have reached the monthly limit for transactions\non your account.\nverify: The payment\nis pending because you are not yet verified. You must verify your\naccount before you can accept this payment.\nother: The payment\nis pending for a reason other than those listed above. For more\ninformation, contact PayPal Customer Service.",
					'protection_eligibility' => "ExpandedSellerProtection:\nSeller is protected by Expanded seller protection\nSellerProtection:\nSeller is protected by PayPal's Seller Protection Policy\nNone:\nSeller is not protected under Expanded seller protection nor the Seller\nProtection Policy",
					'quantity' => "Quantity as entered by your customer or\nas passed by you, the merchant. If this is a shopping cart transaction,\nPayPal appends the number of the item (e.g. quantity1, quantity2).",
					'reason_code' => "This variable is set if payment_status is Reversed, Refunded, Canceled_Reversal, or Denied.\nadjustment_reversal: Reversal of an adjustment.\nadmin_fraud_reversal: The transaction has been reversed due to fraud detected by PayPal administrators.\nadmin_reversal: The transaction has been reversed by PayPal administrators.\nbuyer-complaint: The transaction has been reversed due to a complaint from your customer.\nchargeback: The transaction has been reversed due to a chargeback by your customer.\nchargeback_reimbursement: Reimbursement for a chargeback.\nchargeback_settlement: Settlement of a chargeback.\nguarantee: The transaction has been reversed because your customer exercised a money-back guarantee.\nother: Unspecified reason.\n\nrefund: The transaction has been reversed because you gave the customer a refund.\n\nregulatory_block: PayPal  blocked the transaction due to a violation of a government regulation. In this case, payment_status is Denied.\nregulatory_reject: PayPal  rejected the transaction due to a violation of a government regulation and returned the funds to the buyer. In this case, payment_status is Denied.\nregulatory_review_exceeding_sla: PayPal did not complete the    review   for compliance with government regulations within 72 hours, as required. Consequently, PayPal auto-reversed the transaction and returned the funds to the buyer. In this case, payment_status is Denied. Note that \"sla\" stand for \"service level agreement\".\nunauthorized_claim: The transaction has been reversed because  it was not authorized by the buyer.\nunauthorized_spoof: The transaction has been reversed due to a customer dispute in which an unauthorized spoof is suspected.\n\nNote: Additional codes may be returned.",
					'remaining_settle' => "Remaining amount that can be captured with\nAuthorization and Capture",
					'settle_amount' => "Amount that is deposited into the account's\nprimary balance after a currency conversion from automatic conversion\n(through your Payment Receiving Preferences) or manual conversion\n(through manually accepting a payment).",
					'settle_currency' => 'Currency of settle_amount.',
					'shipping' => "Shipping charges associated with this transaction.Format:\nunsigned, no currency symbol, two decimal places.",
					'shipping_method' => "The name of a shipping method from the Shipping\nCalculations section of the merchant's account profile. The buyer\nselected the named shipping method for this transaction.",
					'tax' => "Amount of tax charged on payment. PayPal\nappends the number of the item (e.g., item_name1, item_name2).\nThe taxx variable is included only\nif there was a specific tax amount applied to a particular shopping cart\nitem. Because total tax may apply to other items in the cart, the\nsum of taxx might not total\nto tax.",
					'transaction_entity' => 'Authorization and Capture transaction entity'
				]
			],
			[
				'name' => 'Recurring Payments',
				'desc' => 'Recurring payments information identifies the amounts and status associated with recurring payments transactions.',
				'fields' => [
					'amount' => 'Amount of recurring payment',
					'amount_per_cycle' => 'Amount of recurring payment per cycle',
					'initial_payment_amount' => 'Initial payment amount for recurring payments',
					'next_payment_date' => 'Next payment date for a recurring payment',
					'outstanding_balance' => 'Outstanding balance for recurring payments',
					'payment_cycle' => 'Payment cycle for recurring payments',
					'period_type' => 'Kind of period for a recurring payment',
					'product_name' => "Product name associated with a recurring\npayment",
					'product_type' => "Product name associated with a recurring\npayment",
					'profile_status' => 'Profile status for a recurring payment',
					'recurring_payment_id' => 'Recurring payment ID',
					'rp_invoice_id' => "The merchant's own unique reference or invoice\nnumber, which can be used to uniquely identify a profile.Length:\n127 single-byte alphanumeric characters",
					'time_created' => 'When a recurring payment was created'
				]
			],
			[
				'name' => 'Dispute resolution variables',
				'desc' => 'Dispute resolution information identifies the case ID and status associated with a dispute.',
				'fields' => [
					'buyer_additional_information' => 'Notes the buyer entered into the Resolution Center.',
					'case_creation_date' => "Date and time case was registered, in the\nfollowing format: HH:MM:SS DD Mmm YY, YYYY PST",
					'case_id' => "Case identification number.Format:\nPP-D-nD-nn-nnn-nnn-nnn where n is any numeric character. Case identification number.\nImportant: There are now two formats that are accepted for the \ncase_id variable. PayPal is enhancing their dispute management system to provide more\ndetails regarding dispute IPNs. The complete transition of this change could take a few years, so\nduring the transition, both formats will be used by the system. \n\n  \nOriginal Format: PP-nnn-nnn-nnn-nnn where n is any numeric character.\n  \nNew Format: PP-D-xxxx where xxxx is an integer, and the \"D\" indicates a dispute.\n\n\nBecause of this change, you will need to make sure that your IPN integration can\naccept and process both formats. As a best practice, you are encouraged to integrate\nflexibly so that any future changes to IPN value parameters can be made without the need for\nintegration change.",
					'case_type' => "chargeback: A buyer\nhas filed a chargeback with his credit card company, which has notified\nPayPal of the reason for the chargeback.\n\n\ncomplaint: A buyer has logged a complaint\nthrough the PayPal Resolution Center.\n\n\ndispute: A buyer and seller post communications\nto one another through the Resolution Center to try to work out\nissues without intervention by PayPal.\n\n\nbankreturn: An ACH return was initiated from the \nbuyer's bank, and the money was removed from the seller's PayPal account.",
					'reason_code' => "Reason for the case.Values for case_type set\nto complaint:\n\n\nnon_receipt:\nBuyer claims that he did not receive goods or service.\n\n\nnot_as_described: Buyer claims that the\ngoods or service received differ from merchant's description of\nthe goods or service.\n\nunauthorized_claim: Buyer claims that an unauthorized payment was  made for this particular transaction.\n\nValues for case_type set\nto chargeback:\n\nunauthorized\n\n\nadjustment_reimburse: A case that has been\nresolved and closed requires a reimbursement.\n\n\nnon_receipt: Buyer claims that he did not\nreceive goods or service.\n\n\nduplicate: Buyer claims that a possible\nduplicate payment was made to the merchant.\n\n\nmerchandise: Buyer claims that the received\nmerchandise is unsatisfactory, defective, or damaged.\n\n\nbilling: Buyer claims that the received\nmerchandise is unsatisfactory, defective, or damaged.\n\n\nspecial: Some other reason. Usually, special\nindicates a credit card processing error for which the merchant\nis not responsible and for which no debit to the merchant will result.\nPayPal must review the documentation from the credit card company\nto determine the nature of the dispute and possibly contact the\nmerchant to resolve it."
				]
			],
			[
				'name' => 'Payment Messages',
				'description' => '',
				'fields' => [
					'transaction_type' => "The type of transaction. Possible values\nare:\n\n\nAdaptive Payment PAY\nThis\nnotification occurs when is a payment is made due to a Pay Request. The\nvariables for the Adaptive Payment Pay notification\nare similar to the PaymentDetailsResponse fields.\n\n\nAdjustmentThis can be for a chargeback,\nreversal, or refund; check the reason_code to see\nwhich it is.",
					'status' => "The status of the payment. Possible values are:\n\n\nCANCELED - The Preapproval agreement was cancelled\n\nCREATED - The payment request was received; funds will be transferred once the payment is approved\n\nCOMPLETED - The payment was successful\n\nINCOMPLETE - Some transfers succeeded and some failed for a parallel payment or, for a delayed chained payment, secondary receivers have not been paid\n\nERROR - The payment failed and all attempted transfers failed or all completed transfers were successfully reversed \n\nREVERSALERROR - One or more transfers failed when attempting to reverse a payment\n\nPROCESSING - The payment is in progress\n\nPENDING - The payment is awaiting processing",
					'sender_email' => "Sender's email address.",
					'action_type' => "Whether the Pay API is\nused with or without the SetPaymentOptions and ExecutePayment API\noperations. Possible values are:\n\n\nPAY -\nIf you are not using the SetPaymentOptions and ExecutePayment API\noperations\n\n\nCREATE - If you are using the SetPaymentOptions and ExecutePayment API\noperations",
					'payment_request_date' => "The date on which the payment request was\ninitiated.",
					'reverse_all_parallel_payments_on_error' => "Whether the payment request specified to\nreverse parallel payments if an error occurs. Possible values are:\n\n\ntrue - Each parallel payment is reversed\nif an error occurs \n\n\nfalse - Only incomplete payments are reversed\n(default)",
					'transaction[n].id' => "The transaction ID, where [n] is a number\nfrom 0 to 5. For simple, single receiver payments, this number will\nbe 0. Numbers larger than 0 indicate the payment to a particular\nreceiver in chained and parallel payments.",
					'transaction[n].status' => "The transaction status, where [n] is a number\nfrom 0 to 5. For simple single-receiver payments, this number will\nbe 0. Numbers larger than 0 indicate the payment to a particular\nreceiver in chained and parallel payments.Possible values\nare:\n\nCompleted\n\nPending\n\nRefunded",
					'transaction[n].id_for_sender' => "The transaction ID for the sender, where\n[n] is a number from 0 to 5. For simple, single receiver payments,\nthis number will be 0. Numbers larger than 0 indicate the payment\nto a particular receiver in chained and parallel payments.",
					'transaction[n].status_for_sender_txn' => "The transaction status, where [n] is a number\nfrom 0 to 5. For simple single-receiver payments, this number will\nbe 0. Numbers larger than 0 indicate the payment to a particular\nreceiver in chained and parallel payments.Possible values\nare:\n\n\nCOMPLETED - The sender's transaction\nhas completed\n\n\nPENDING - The transaction is awaiting further\nprocessing\n\n\nCREATED - The payment request was received;\nfunds will be transferred once approval is received\n\n\nPARTIALLY_REFUNDED - Transaction was partially\nrefunded\n\n\nDENIED - The transaction was rejected by\nthe receiver\n\n\nPROCESSING - The transaction is in progress\n\n\nREVERSED - The payment was returned to the\nsender\n\n\nREFUNDED - The payment was refunded\n\n\nFAILED - The payment failed",
					'transaction[n].refund_id' => 'The identification number for the refund',
					'transaction[n].refund_amount' => 'The amount that was refunded.',
					'transaction[n].refund_account_charged' => "The email address of the debit account of\nthe refund.",
					'transaction[n].receiver' => "The receiver's email address for the transaction",
					'transaction[n].invoiceId' => 'The invoice number for this transaction',
					'transaction[n].amount' => 'The payment amount of the transaction',
					'transaction[n].is_primary_receiver' => "Whether there is a primary receiver for\nthis transaction, which indicates whether the transaction is a chained\npayment. Possible values are:\n\n\ntrue -\nThere is a primary receiver (chained payment)\n\n\nfalse - There is no primary receiver (simple\nor parallel payment)",
					'return_url' => "The URL to which the sender's browser is\nredirected after approving a payment on paypal.com. Use the pay\nkey to identify the payment as follows: payKey=\${payKey}.",
					'cancel_url' => "The URL to which the sender's browser is\nredirected if the sender cancels the approval for a payment on paypal.com.\nUse the pay key to identify the payment as follows: payKey=\${payKey}.",
					'ipn_notification_url' => "The URL to which all IPN messages for this\npayment are sent.",
					'pay_key' => "The pay key that identifies this payment.\nThis is a token that is assigned by the Pay API after a PayRequest message\nis received and can be used in other Adaptive Payments APIs as well\nas the cancelURL and returnURL to identify\nthis payment. The pay key is valid for 3 hours.",
					'memo' => 'A note associated with the payment.',
					'fees_payer' => "The payer of PayPal fees. Possible values\nare:\n\n\nSENDER - Sender pays all fees\n(for personal, implicit simple/parallel payments; do not use for\nchained or unilateral payments)\n\n\nPRIMARYRECEIVER - Primary receiver pays\nall fees (chained payments only)\n\n\nEACHRECEIVER - Each receiver pays their\nown fee (default, personal and unilateral payments)\n\n\nSECONDARYONLY - Secondary receivers pay\nall fees (use only for chained payments with one secondary receiver)",
					'trackingId' => "The tracking ID that was specified for this\npayment in the PaymentDetailsRequest message.",
					'preapproval_key' => "The preapproval key returned after a PreapprovalRequest,\nor the preapproval key that identifies the preapproval key sent\nwith a PayRequest.",
					'reason_code' => "Whether this transaction is a chargeback,\npartial, or reversal. Possible values are:\n\n\nChargeback\nSettlement - Transaction is a chargeback\n\n\nAdmin reversal - Transaction was reversed\nby PayPal administrators\n\n\nRefund - Transaction was partially or fully\nrefunded"
				]
			],
			[
				'name' => 'Preapproval Messages',
				'description' => '',
				'fields' => [
					'transaction_type' => "The type of transaction. For a preapproval,\nthis variable returns Adaptive Payment Preapproval.\n\nNote: If\nthis variable is set to Adaptive Payment Pay or Adjustment, refer\nto the Pay Message Variable section.",
					'preapproval_key' => 'The preapproval key returned after a PreapprovalRequest.',
					'approved' => "Whether the preapproval request was approved.\nPossible values are:\n\n\ntrue - The preapproval\nwas approved\n\n\nfalse - The preapproval was denied",
					'cancel_url' => "The URL to which the sender's browser is\nredirected if the sender decides to cancel the preapproval as requested.\nUse the preapproval key to identify the payment as follows: preapprovalKey=\${preapprovalKey}",
					'current_number_of_payments' => "The current number of payments made for\nthis preapproval.",
					'current_total_amount_of_all_payments' => "The current total of payments made for this\npreapproval.",
					'current_period_attempts' => "The current number of attempts this period\nfor this preapproval.",
					'currency_code' => "The currency code. Possible values are:\n\nAustralian\nDollar - AUD\n\n\nBrazilian Real - BRL\n\nNote: The Real\nis supported as a payment currency and currency balance only for Brazilian\nPayPal accounts. \n\n\nCanadian Dollar - CAD\n\n\nCzech Koruna - CZK\n\n\nDanish Krone - DKK\n\n\nEuro - EUR\n\n\nHong Kong Dollar - HKD\n\n\nHungarian Forint - HUF\n\n\nIsraeli New Sheqel - ILS\n\n\nJapanese Yen - JPY\n\n\nMalaysian Ringgit - MYR\n\nNote: The\nRinggit is supported as a payment currency and currency balance\nonly for Malaysian PayPal accounts. \n\n\nMexican Peso - MXN\n\n\nNorwegian Krone - NOK\n\n\nNew Zealand Dollar - NZD\n\n\nPhilippine Peso - PHP\n\n\nPolish Zloty - PLN\n\n\nPound Sterling - GBP\n\n\nSingapore Dollar - SGD\n\n\nSwedish Krona - SEK\n\n\nSwiss Franc - CHF\n\n\nTaiwan New Dollar - TWD\n\n\nThai Baht - THB\n\n\nTurkish Lira - TRY\n\nNote: The Turkish\nLira is supported as a payment currency and currency balance only\nfor Turkish PayPal accounts. \n\n\nU.S. Dollar - USD",
					'date_of_month' => "The day of the month on which a monthly\npayment is to be made. A number between 1 and 31 indicates the day\nof the month. A value of 0 indicates that the payment can be made\non any day.",
					'day_of_week' => "The day of the week that a weekly payment\nis to be made. Possible values are:\n\nNO_DAY_SPECIFIED\n\nSUNDAY\n\nMONDAY\n\nTUESDAY\n\nWEDNESDAY\n\nTHURSDAY\n\nFRIDAY\n\nSATURDAY",
					'starting_date' => "First date for which the preapproval is\nvalid.",
					'ending_date' => "Last date for which the preapproval is valid.\nTime is currently not supported.",
					'max_total_amount_of_all_payments' => "The pre-approved maximum total amount of\nall payments.",
					'max_amount_per_payment' => 'The pre-approved maximum amount of all payments.',
					'max_number_of_payments' => 'The maximum number of payments that is pre-approved.',
					'payment_period' => "The payment period. Possible values\nare:\n\nNO_PERIOD_SPECIFIED\n\nDAILY\n\nWEEKLY\n\nBIWEEKLY\n\nSEMIMONTHLY\n\nMONTHLY\n\nANNUALLY",
					'pin_type' => "Whether a personal identification number\n(PIN) is required. It is one of the following values:\n\n\nNOT_REQUIRED -\nA PIN is not required\n\n\nREQUIRED - A PIN is required",
					'sender_email' => "The sender's email address."
				]
			],
			[
				'name' => 'Subscription Information',
				'desc' => 'Subscription information identifies the amounts and parameters associated with subscription transactions.',
				'fields' => [
					'amount1' => "Amount of payment for trial period 1 for\nUSD payments; otherwise blank (optional).",
					'amount2' => "Amount of payment for trial period 2 for\nUSD payments; otherwise blank (optional).",
					'amount3' => "Amount of payment for regular subscription\nperiod for USD payments; otherwise blank.",
					'mc_amount1' => "Amount of payment for trial period 1, regardless\nof currency (optional).",
					'mc_amount2' => "Amount of payment for trial period 2, regardless\nof currency (optional).",
					'mc_amount3' => "Amount of payment for regular subscription\nperiod, regardless of currency.",
					'password' => "(optional) Password generated by PayPal\nand given to subscriber to access the subscription (password will\nbe encrypted).Length: 24 characters",
					'period1' => "(optional) Trial subscription interval in\ndays, weeks, months, years (example: a 4 day interval is \"period1:\n4 D\").",
					'period2' => "(optional) Trial subscription interval in\ndays, weeks, months, or years.",
					'period3' => "Regular subscription interval in days, weeks,\nmonths, or years.",
					'reattempt' => "Indicates whether reattempts should occur\nupon payment failures (1 is yes, blank is no).",
					'recur_times' => "The number of payment installments that\nwill occur at the regular rate.",
					'recurring' => "Indicates whether regular rate recurs (1\nis yes, blank is no).",
					'retry_at' => "Date PayPal will retry a failed subscription\npayment.",
					'subscr_date' => "Start date or cancellation date depending\non whether transaction is subscr_signup or subscr_cancel.Time/Date\nstamp generated by PayPal, in the following format: HH:MM:SS DD\nMmm YY, YYYY PST",
					'subscr_effective' => "Date when the subscription modification\nwill be effective (only for txn_type = subscr_modify).Time/Date\nstamp generated by PayPal, in the following format: HH:MM:SS DD\nMmm YY, YYYY PST",
					'subscr_id' => "ID generated by PayPal for the subscriber.Length:\n19 characters",
					'username' => "(optional) Username generated by PayPal\nand given to subscriber to access the subscription.Length:\n64 characters"
				]
			],
			[
				'name' => 'Misc Information',
				'desc' => 'Assorted information that doesnt really fit anywher else.',
				'fields' => [
					'correlation_id',
					'currency_code',
					'discount',
					'ebay_txn_id1',
					'handling_amount',
					'id',
					'insurance_amount',
					'item_count_unit1',
					'item_isbn1',
					'item_model_number1',
					'item_mpn1',
					'item_plu1',
					'item_style_number1',
					'item_taxable1',
					'item_tax_rate1',
					'item_tax_rate_double1',
					'lid',
					'custid',
					'locked',
					'shipping_discount',
					'transaction_subject',
					'when'
				]
			],
			[
				'name' => 'Auction Information',
				'desc' => 'Auction information identifies the auction for which a payment is made and additional information about the auction.',
				'fields' => [
					'auction_buyer_id',
					'auction_closing_date',
					'for_auction'
				]
			],
			/*array(
				'name' => 'Mass Pay Information',
				'fields' => array(
					"masspay_txn_id_x" => "For Mass Payments, a unique transaction\nID generated by the PayPal system, where x is\nthe record number of the mass pay itemLength: 19 characters",
					"mc_currency_x" => "For Mass Payments, the currency of the amount\nand fee, where x is the record number the mass\npay item",
					"mc_fee_x" => "For Mass Payments, the transaction fee associated\nwith the payment, where x is the record number\nthe mass pay item",
					"mc_gross_x" => "The gross amount for the amount, where x is\nthe record number the mass pay item",
					"mc_handlingx" => "The x is\nthe shopping cart detail item number. The handling_cart cart-wide\nWebsite Payments variable is also included in the mc_handling variable;\nfor this reason, the sum of mc_handlingx might\nnot be equal to mc_handling",
					"payment_date" => "For Mass Payments, the first IPN is the\ndate/time when the record set is processed. Format: HH:MM:SS DD\nMmm YYYY PSTLength: 28 characters",
					"payment_status" => "Completed: For Mass Payments,\nthis means that all of your payments have been claimed, or after\na period of 30 days, unclaimed payments have been returned to you.Denied:\nFor Mass Payments, this means that your funds were not sent and\nthe Mass Payment was not initiated. This may have been caused by lack\nof funds.Processed: Your Mass Payment has\nbeen processed and all payments have been sent.",
					"reason_code_x" => "This variable is set only if status = Failed.\n    1001 Receiver's account is invalid\n    1002 Sender has insufficient funds\n    1003 User's country is not allowed\n    1004 User's credit card is not in the list of allowed countries of the gaming merchant\n    3004 Cannot pay self\n    3014 Sender's account is locked or inactive\n    3015 Receiver's account is locked or inactive \n    3016 Either the sender or receiver exceeded the transaction limit\n    3017 Spending limit exceeded\n    3047 User is restricted\n    3078 Negative balance\n    3148 Receiver's address is in a non-receivable country or a PayPal zero country\n    3535 Invalid currency\n    3547 Sender's address is located in a restricted State \n    3558 Receiver's address is located in a restricted State \n    3769 Market closed and transaction is between 2 different countries\n    4001 Internal error\n    4002 Internal error\n    8319 Zero amount\n    8330 Receiving limit exceeded\n    8331 Duplicate Mass payment\n    9302 Transaction was declined \n    11711 Per-transaction sending limit exceeded\n    14159 Transaction currency cannot be received by the recipient\n    14550 Currency compliance\n    14764 Regulatory review - Pending\n    14765 Regulatory review - Blocked\n    14767 Receiver is unregistered\n    14768 Receiver is unconfirmed\n    14769 Youth account recipient\n    14800 POS cumulative sending limit exceeded",
					"receiver_email_x" => "For Mass Payments, the primary email address\nof the payment recipient, where x is the record\nnumber of the mass pay item.Length: 127 characters",
					"status_x" => "For Mass Payments, the status of the payment,\nwhere x is the record number\nCompleted: The payment has been processed, regardless of whether this was originally\na unilateral payment\nFailed: The payment failed because of an insufficient PayPal balance.\nReturned: When an unclaimed payment remains unclaimed for more than 30 days, it is returned to the sender.\nReversed: PayPal has reversed the transaction.\nUnclaimed: This is for unilateral payments that are unclaimed.\nPending:  The payment is pending because it is being reviewed for compliance with government regulations. The review will be completed and the payment status will be updated within 72 hours. \nBlocked: This payment was blocked due to a violation of government regulations.",
					"unique_id_x" => "For Mass Payments, the unique ID from input,\nwhere x is the record number. This allows the\nmerchant to cross-reference the paymentLength: 13 characters"
				),
			),*/
			/*array(
				'name' => 'Adaptive IPN Messages',
				'description' => '',
				'fields' => array(
					"notify_version" => "Message's version number",
					"first_name" => "Account holder's first name",
					"last_name" => "Account holder's last name",
					"verify_sign" => "Encrypted string used to validate the authenticity\nof the transaction",
					"charset" => "Character set",
					"account_key" => "Account key returned by the CreateAccount API\noperation",
					"confirmation_code" => "Confirmation code",
					"event_type" => "The kind of event:\n\n\nACCOUNT_CONFIRMED indicates\nthat the account holder has set a password and the account has been\ncreated. \n\n\nLOGIN_CONFIRMED indicates that the account\nholder logged into the account."
				),
			),*/
		];
}

/**
 * view_paypal_transaction()
 *
 * @return bool|void
 * @throws \Exception
 * @throws \SmartyException
 */
	function view_paypal_transaction()
	{
		page_title('PayPal Transaction Information');
		function_requirements('has_acl');
		if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
			dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
			return false;
		}
		add_js('bootstrap');
		add_js('font-awesome');
		add_js('isotope');
		$GLOBALS['body_extra'] = ' data-spy="scroll" data-target="#scrollspy" style="position: relative;"';
		$GLOBALS['tf']->add_html_head_css_file('/css/view_paypal_transaction.css');
		$GLOBALS['tf']->add_html_head_js_file('/js/view_paypal_transaction.js');
		$transaction_types = get_paypal_transaction_types();
		$cats = get_paypal_cats_and_fields();
		$db = clone $GLOBALS['tf']->db;
		$module = get_module_name((isset($GLOBALS['tf']->variables->request['module']) ? $GLOBALS['tf']->variables->request['module'] : 'default'));
		if (isset($GLOBALS['tf']->variables->request['transaction'])) {
			$transaction = $db->real_escape($GLOBALS['tf']->variables->request['transaction']);
			$query = "select * from paypal where txn_id='{$transaction}'";
		} elseif (isset($GLOBALS['tf']->variables->request['payer_id'])) {
			$payer_id = $db->real_escape($GLOBALS['tf']->variables->request['payer_id']);
			$query = "select * from paypal where payer_id='{$payer_id}'";
		} elseif (isset($GLOBALS['tf']->variables->request['payer_email'])) {
			$payer_email = $db->real_escape($GLOBALS['tf']->variables->request['payer_email']);
			$query = "select * from paypal where payer_email='{$payer_email}'";
		} elseif (isset($GLOBALS['tf']->variables->request['recurring_payment_id'])) {
			$recurring_payment_id = $db->real_escape($GLOBALS['tf']->variables->request['recurring_payment_id']);
			$query = "select * from paypal where recurring_payment_id='{$recurring_payment_id}'";
		}
		$db->query($query);
		if ($db->num_rows() == 0) {
			$db = get_module_db($module);
			$db->query($query);
		}
		if ($db->num_rows() > 0) {
			$table = new TFtable;
			$transactions = [];
			$smarty = new TFSmarty;
			while ($db->next_record(MYSQL_ASSOC)) {
				$transaction = [];
				foreach ($db->Record as $key => $value) {
					if ($key == 'lid') {
						$transaction[$key] = $table->make_link('choice=none.edit_customer&amp;lid='.$value, $value, false, 'target="_blank" title="Edit Customer"');
					} elseif ($key == 'custid') {
						$transaction[$key] = $value == 0 ? '' : $table->make_link('choice=none.edit_customer&amp;customer='.$value, $GLOBALS['tf']->accounts->cross_reference($value), false, 'target="_blank" title="Edit Customer"');
					} elseif ($key == 'payer_email' || $key == 'payer_id' || $key == 'recurring_payment_id') {
						$transaction[$key] = $table->make_link('choice=none.view_paypal_transaction&amp;'.$key.'='.$value, $value, false, 'target="_blank" title="View Payers Transactions"');
					} elseif ($key == 'txn_type' && isset($transaction_types[$value])) {
						$transaction[$key] = '<strong title="'.htmlspecial($transaction_types[$value]).'">'.$value.'</strong>';
					} elseif (in_array($key, ['verify_sign'])) {
						$transaction[$key] = wordwrap($value, 28, '<br>', true);
					} elseif ($key == 'custom') {
						if (preg_match('/^COMPRESSED(?P<data>.+)$/', $value, $matches)) {
							$orig_value = $value;
							$value = gzuncompress(base64_decode(str_replace(' ', '+', $matches['data'])));
							myadmin_log('admin', 'info', "Uncompressed Custom To - {$value}", __LINE__, __FILE__);
							if ($value == '') {
								myadmin_log('admin', 'info', "Reverting Blank Custom To {$orig_value}", __LINE__, __FILE__);
								$value = $orig_value;
								$value = gzdecode(base64_decode(str_replace(' ', '+', $matches['data'])));
								myadmin_log('admin', 'info', "Uncompressed Custom To - {$value}", __LINE__, __FILE__);
								if ($value == '') {
									myadmin_log('admin', 'info', "Reverting Blank Custom To {$orig_value}", __LINE__, __FILE__);
									$value = $orig_value;
								}
							}
						}
						$invoices = explode(',', $value);
						foreach ($invoices as $idx => $invoice) {
							if (preg_match('/^SERVICE(?P<module>\D+)(?P<id>\d+)$/', $invoice, $matches)) {
								$module = $matches['module'];
								$service = $GLOBALS['tf']->db->real_escape($matches['id']);
								if ($module == 'vps') {
									$suffix = '3';
								} elseif ($module == 'webhosting') {
									$suffix = '2';
								} else {
									$suffix = '';
								}
								$invoices[$idx] = $table->make_link('choice=none.view_'.$GLOBALS['modules'][$module]['PREFIX'].$suffix.'&amp;id='.$service, $invoice, false, 'target="_blank" title="View '.$GLOBALS['modules'][$module]['TBLNAME'].' '.$service.' Details"');
							}
						}
						//$transaction[$key] = wordwrap(implode(',', $invoices), 28, '<br>');
						$transaction[$key] = implode(',', $invoices);
					}
					//elseif ($key == 'lid')
					//$transaction[$key] = $table->make_link('choice=none.search&amp;search=' . urlencode($value), $value, 'Search for "'.$value.'"', 'target="_blank"');
					else {
						$transaction[$key] = $value;
					}
				}
				//print_r($db->Record);
				$transactions[] = $transaction;
			}
			$smarty->assign('transaction', $transaction);
			$smarty->assign('transactions', $transactions);
			$smarty->assign('paypal_cats', $cats);
			add_output($smarty->fetch('view_paypal_transaction.tpl'));
		}
	}
