<?php
/**
 * PayPal Related Functionality
 * Last Changed: $LastChangedDate: 2017-08-07 01:08:50 -0400 (Mon, 07 Aug 2017) $
 * @author detain
 * @copyright 2017
 * @package MyAdmin
 * @category PayPal
 */

/**
 * gets the paypal fields for adaptive account ipn messages
 *
 * @return array an array of field name => description
 */
function get_paypal_adaptive_accounts_ipn_messages() {
	return [
		'notify_version' => 'Messages version number',
		'first_name' => 'Account holders first name',
		'last_name' => 'Account holders last name',
		'verify_sign' => 'Encrypted string used to validate the authenticity of the transaction',
		'charset' => 'Character set',
		'account_key' => 'Account key returned by the CreateAccount API operation',
		'confirmation_code' => 'Confirmation code',
		'event_type' => 'The kind of event:   ACCOUNT_CONFIRMED indicates that the account holder has set a password and the account has been created.    LOGIN_CONFIRMED indicates that the account holder logged into the account.'
	];
}

/**
 * gets the paypal fields for auction vars
 *
 * @return array an array of field name => description
 */
function get_paypal_auction_vars() {
	return [
		'auction_buyer_id' => 'The customers auction ID.Length: 64 characters',
		'auction_closing_date' => 'The auctions close date, in the following format: HH:MM:SS DD Mmm YY, YYYY PSTLength: 28 characters',
		'auction_multi_item' => 'The number of items purchased in multi-item auction payments. It allows you to count the mc_gross or payment_gross for the first IPN you receive from a multi-item auction (auction_multi_item), since each item from the auction will generate an Instant Payment Notification showing the amount for the entire auction.',
		'for_auction' => 'This is an auction payment-payments made using Pay for eBay Items or Smart Logos-as well as Send Money/Money Request payments with the type eBay items or Auction Goods (non-eBay).'
	];
}

/**
 * gets the paypal fields for buyer information
 *
 * @return array an array of field name => description
 */
function get_paypal_buyer_information_vars() {
	return [
		'address_country' => 'Country of customers addressLength: 64 characters',
		'address_city' => 'City of customers addressLength: 40 characters',
		'address_country_code' => 'ISO 3166 country code associated with customers addressLength: 2 characters',
		'address_name' => 'Name used with address (included when the customer provides a Gift Address)Length: 128 characters',
		'address_state' => 'State of customers addressLength: 40 characters',
		'address_status' => 'Whether the customer provided a confirmed address. It is one of the following values:   confirmed - Customer provided a confirmed address.   unconfirmed - Customer provided an unconfirmed address.',
		'address_street' => 'Customers street address.Length: 200 characters',
		'address_zip' => 'Zip code of customers address.Length: 20 characters',
		'contact_phone' => 'Customers telephone number.Length: 20 characters',
		'first_name' => 'Customers first nameLength: 64 characters',
		'last_name' => 'Customers last nameLength: 64 characters',
		'payer_business_name' => 'Customers company name, if customer is a businessLength: 127 characters',
		'payer_email' => 'Customers primary email address. Use this email to provide any credits.Length: 127 characters',
		'payer_id' => 'Unique customer ID.Length: 13 characters'
	];
}

/**
 * gets the paypal fields for dispute resolution
 *
 * @return array an array of field name => description
 */
function get_paypal_dispute_resolution_vars() {
	return [
		'buyer_additional_information' => 'Notes the buyer entered into the Resolution Center.',
		'case_creation_date' => 'Date and time case was registered, in the following format: HH:MM:SS DD Mmm YY, YYYY PST',
		'case_id' => 'Case identification number.Format: PP-D-nD-nn-nnn-nnn-nnn where n is any numeric character. Important: There are now two formats that are accepted for the  case_id variable. PayPal is enhancing their dispute management system to provide more details regarding dispute IPNs. The complete transition of this change could take a few years, so during the transition, both formats will be used by the system.      Original Format: PP-nnn-nnn-nnn-nnn where n is any numeric character.    New Format: PP-D-xxxx where xxxx is an integer, and the "D" indicates a dispute.   Because of this change, you will need to make sure that your IPN integration can accept and process both formats. As a best practice, you are encouraged to integrate flexibly so that any future changes to IPN value parameters can be made without the need for integration change.',
		'case_type' => 'chargeback: A buyer has filed a chargeback with his credit card company, which has notified PayPal of the reason for the chargeback.   complaint: A buyer has logged a complaint through the PayPal Resolution Center.   dispute: A buyer and seller post communications to one another through the Resolution Center to try to work out issues without intervention by PayPal.   bankreturn: An ACH return was initiated from the  buyers bank, and the money was removed from the sellers PayPal account.',
		'reason_code' => 'Reason for the case.Values for case_type set to complaint:   non_receipt: Buyer claims that he did not receive goods or service.   not_as_described: Buyer claims that the goods or service received differ from merchants description of the goods or service.  unauthorized_claim: Buyer claims that an unauthorized payment was  made for this particular transaction.  Values for case_type set to chargeback:  unauthorized   adjustment_reimburse: A case that has been resolved and closed requires a reimbursement.   non_receipt: Buyer claims that he did not receive goods or service.   duplicate: Buyer claims that a possible duplicate payment was made to the merchant.   merchandise: Buyer claims that the received merchandise is unsatisfactory, defective, or damaged.   billing: Buyer claims that the received merchandise is unsatisfactory, defective, or damaged.   special: Some other reason. Usually, special indicates a credit card processing error for which the merchant is not responsible and for which no debit to the merchant will result. PayPal must review the documentation from the credit card company to determine the nature of the dispute and possibly contact the merchant to resolve it.'
	];
}

/**
 * gets the paypal fields for global shipping
 *
 * @return array an array of field name => description
 */
function get_paypal_global_shipping_vars() {
	return [
		'fulfillment_address_country' => 'Country of fulfillment center address Length: 64 characters',
		'fulfillment_address_city' => 'City of fulfillment center address Length: 40 characters',
		'fulfillment_address_country_code' => 'ISO 3166 country code associated with fulfillment center address Length: 2 characters',
		'fulfillment_address_name' => 'Name used with fulfillment center address Length: 128 characters',
		'fulfillment_address_state' => 'State of fulfillment center address Length: 40 characters',
		'fulfillment_address_street' => 'Street of fulfillment center address Length: 200 characters',
		'fulfillment_address_zip' => 'Zip code of fulfillment center address Length: 20 characters'
	];
}

/**
 * gets the paypal fields for mass payments
 *
 * @return array an array of field name => description
 */
function get_paypal_mass_pay_vars() {
	return [
		'masspay_txn_id_x' => 'For Mass Payments, a unique transaction ID generated by the PayPal system, where x is the record number of the mass pay itemLength: 19 characters',
		'mc_currency_x' => 'For Mass Payments, the currency of the amount and fee, where x is the record number the mass pay item',
		'mc_fee_x' => 'For Mass Payments, the transaction fee associated with the payment, where x is the record number the mass pay item',
		'mc_gross_x' => 'The gross amount for the amount, where x is the record number the mass pay item',
		'mc_handlingx' => 'The x is the shopping cart detail item number. The handling_cart cart-wide Website Payments variable is also included in the mc_handling variable; for this reason, the sum of mc_handlingx might not be equal to mc_handling',
		'payment_date' => 'For Mass Payments, the first IPN is the date/time when the record set is processed. Format: HH:MM:SS DD Mmm YYYY PSTLength: 28 characters',
		'payment_status' => 'Completed: For Mass Payments, this means that all of your payments have been claimed, or after a period of 30 days, unclaimed payments have been returned to you.Denied: For Mass Payments, this means that your funds were not sent and the Mass Payment was not initiated. This may have been caused by lack of funds.Processed: Your Mass Payment has been processed and all payments have been sent.',
		'reason_code_x' => 'This variable is set only if status = Failed.     1001 Receivers account is invalid     1002 Sender has insufficient funds     1003 Users country is not allowed     1004 Users credit card is not in the list of allowed countries of the gaming merchant     3004 Cannot pay self     3014 Senders account is locked or inactive     3015 Receivers account is locked or inactive      3016 Either the sender or receiver exceeded the transaction limit     3017 Spending limit exceeded     3047 User is restricted     3078 Negative balance     3148 Receivers address is in a non-receivable country or a PayPal zero country     3535 Invalid currency     3547 Senders address is located in a restricted State      3558 Receivers address is located in a restricted State      3769 Market closed and transaction is between 2 different countries     4001 Internal error     4002 Internal error     8319 Zero amount     8330 Receiving limit exceeded     8331 Duplicate Mass payment     9302 Transaction was declined      11711 Per-transaction sending limit exceeded     14159 Transaction currency cannot be received by the recipient     14550 Currency compliance     14764 Regulatory review - Pending     14765 Regulatory review - Blocked     14767 Receiver is unregistered     14768 Receiver is unconfirmed     14769 Youth account recipient     14800 POS cumulative sending limit exceeded',
		'receiver_email_x' => 'For Mass Payments, the primary email address of the payment recipient, where x is the record number of the mass pay item.Length: 127 characters',
		'status_x' => 'For Mass Payments, the status of the payment, where x is the record number Completed: The payment has been processed, regardless of whether this was originally a unilateral payment Failed: The payment failed because of an insufficient PayPal balance. Returned: When an unclaimed payment remains unclaimed for more than 30 days, it is returned to the sender. Reversed: PayPal has reversed the transaction. Unclaimed: This is for unilateral payments that are unclaimed. Pending:  The payment is pending because it is being reviewed for compliance with government regulations. The review will be completed and the payment status will be updated within 72 hours.  Blocked: This payment was blocked due to a violation of government regulations.',
		'unique_id_x' => 'For Mass Payments, the unique ID from input, where x is the record number. This allows the merchant to cross-reference the paymentLength: 13 characters'
	];
}

/**
 * gets the paypal fields for payment information
 *
 * @return array an array of field name => description
 */
function get_paypal_payment_information_vars() {
	return [
		'auth_amount' => 'Authorization amount',
		'auth_exp' => 'Authorization expiration date and time, in the following format: HH:MM:SS DD Mmm YY, YYYY PSTLength: 28 characters',
		'auth_id' => 'Authorization identification numberLength: 19 characters',
		'auth_status' => 'Status of authorization',
		'echeck_time_processed' => 'The time an eCheck was processed; for example, when the status changes to Success or Completed. The format is as follows: hh:mm:ss MM DD, YYYY ZONE, e.g. 04:55:30 May 26, 2011 PDT.',
		'exchange_rate' => 'Exchange rate used if a currency conversion occurred.',
		'fraud_management_pending_filters_x' => 'One or more filters that identify a triggering action associated with one of the following payment_status values: Pending, Completed, Denied, where x is a number starting with 1 that makes the IPN variable name unique; x is not the filters ID number. The filters and their ID numbers are as follows:   1 - AVS No Match   2 - AVS Partial Match   3 - AVS Unavailable/Unsupported   4 - Card Security Code (CSC) Mismatch   5 - Maximum Transaction Amount   6 - Unconfirmed Address   7 - Country Monitor   8 - Large Order Number   9 - Billing/Shipping Address Mismatch   10 - Risky ZIP Code   11 - Suspected Freight Forwarder Check   12 - Total Purchase Price Minimum   13 - IP Address Velocity   14 - Risky Email Address Domain Check   15 - Risky Bank Identification Number (BIN) Check   16 - Risky IP Address Range   17 - PayPal Fraud Model',
		'invoice' => 'Pass-through variable you can use to identify your Invoice Number for this purchase. If omitted, no variable is passed back.Length: 127 characters',
		'item_namex' => 'Item name as passed by you, the merchant. Or, if not passed by you, as entered by your customer. If this is a shopping cart transaction, PayPal will append the number of the item (e.g., item_name1, item_name2, and so forth).Length: 127 characters',
		'item_numberx' => 'Pass-through variable for you to track purchases. It will get passed back to you at the completion of the payment. If omitted, no variable will be passed back to you. If this is a shopping cart transaction, PayPal will append the number of the item (e.g., item_number1, item_number2, and so forth)Length: 127 characters',
		'mc_currency' => 'For payment IPN notifications, this is the currency of the payment.   For non-payment subscription IPN notifications (i.e., txn_type= signup, cancel, failed, eot, or modify), this is the currency of the subscription.   For payment subscription IPN notifications, it is the currency of the payment (i.e., txn_type = subscr_payment)',
		'mc_fee' => 'Transaction fee associated with the payment. mc_gross minus mc_fee equals the amount deposited into the receiver_email account. Equivalent to payment_fee for USD payments. If this amount is negative, it signifies a refund or reversal, and either of those payment statuses can be for the full or partial amount of the original transaction fee.',
		'mc_gross' => 'Full amount of the customers payment, before transaction fee is subtracted. Equivalent to payment_gross for USD payments. If this amount is negative, it signifies a refund or reversal, and either of those payment statuses can be for the full or partial amount of the original transaction.',
		'mc_gross_x' => 'The amount is in the currency of mc_currency, where x is the shopping cart detail item number. The sum of mc_gross_x should total mc_gross.',
		'mc_handling' => 'Total handling amount associated with the transaction.',
		'mc_shipping' => 'Total shipping amount associated with the transaction.',
		'mc_shippingx' => 'This is the combined total of shipping1 and shipping2 Website Payments Standard variables, where x is the shopping cart detail item number. The shippingx variable is only shown when the merchant applies a shipping amount for a specific item. Because profile shipping might apply, the sum of shippingx might not be equal to shipping.',
		'memo' => 'Memo as entered by your customer in PayPal Website Payments note field.Length: 255 characters',
		'num_cart_items' => 'If this is a PayPal Shopping Cart transaction, number of items in cart.',
		'option_name1' => 'Option 1 name as requested by you. PayPal appends the number of the item where x represents the number of the shopping cart detail item (e.g., option_name1, option_name2).Length: 64 characters',
		'option_name2' => 'Option 2 name as requested by you. PayPal appends the number of the item where x represents the number of the shopping cart detail item (e.g., option_name2, option_name2).Length: 64 characters',
		'option_selection1' => 'Option 1 choice as entered by your customer.PayPal appends the number of the item where x represents the number of the shopping cart detail item (e.g., option_selection1, option_selection2).Length: 200 characters',
		'option_selection2' => 'Option 2 choice as entered by your customer.PayPal appends the number of the item where x represents the number of the shopping cart detail item (e.g., option_selection1, option_selection2).Length: 200 characters',
		'payer_status' => 'Whether the customer has a verified PayPal account.   verified - Customer has a verified PayPal account.   unverified - Customer has an unverified PayPal account.',
		'payment_date' => 'Time/Date stamp generated by PayPal, in the following format: HH:MM:SS Mmm DD, YYYY PDTLength: 28 characters',
		'payment_fee' => 'USD transaction fee associated with the payment. payment_gross minus payment_fee equals the amount deposited into the receiver email account. Is empty for non-USD payments. If this amount is negative, it signifies a refund or reversal, and either of those payment statuses can be for the full or partial amount of the original transaction fee.  Note: This is a deprecated field. Use mc_fee instead.',
		'payment_fee_x' => 'If the payment is USD, then the value is the same as that for mc_fee_x, where x is the record number; if the currency is not USD, then this is an empty string.  Note: This is a deprecated field. Use mc_fee_x instead.',
		'payment_gross' => 'Full USD amount of the customers payment, before transaction fee is subtracted. Will be empty for non-USD payments. This is a legacy field replaced by mc_gross. If this amount is negative, it signifies a refund or reversal, and either of those payment statuses can be for the full or partial amount of the original transaction.',
		'payment_gross_x' => 'If the payment is USD, then the value for this is the same as that for the mc_gross_x, where x is the record number the mass pay item. If the currency is not USD, this is an empty string.  Note: This is a deprecated field. Use mc_gross_x instead.',
		'payment_status' => 'The status of the payment: Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you. Completed: The payment has been completed, and the funds have been added successfully to your account balance. Created: A German ELV payment is made using Express Checkout. Denied: The payment was denied. This happens only if the payment was previously pending because of one of the reasons listed for the pending_reason variable or the Fraud_Management_Filters_x variable. Expired: This authorization has expired and cannot be captured. Failed: The payment has failed. This happens only if the payment was made from your customers bank account. Pending: The payment is pending. See pending_reason for more information. Refunded: You refunded the payment. Reversed: A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element. Processed: A payment has been accepted. Voided: This authorization has been voided.',
		'payment_type' => 'echeck: This payment was funded with an eCheck.instant: This payment was funded with PayPal balance, credit card, or Instant Transfer.',
		'pending_reason' => 'This variable is set only if payment_status is  Pending. address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set yo allow you to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. authorization: You set the payment action to Authorization and have not yet captured funds. echeck: The payment is pending because it was made by an eCheck that has not yet cleared. intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview. multi_currency: You do not have a balance in the currency sent, and you do not have your profiles Payment Receiving Preferences option set to automatically convert and accept this payment. As a result, you must manually accept or deny this payment. order: You set the payment action to Order and have not yet captured funds. paymentreview: The payment is pending while it is  reviewed by PayPal for risk. regulatory_review:  The payment is pending because PayPal is reviewing it for  compliance with government regulations. PayPal will complete this review within 72 hours. When the review is complete, you will receive a second IPN message whose payment_status/reason code variables  indicate the result. unilateral: The payment is pending because it was made to an email address that is not yet registered or confirmed. upgrade: The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status before you can receive the funds. upgrade can also mean that you have reached the monthly limit for transactions on your account. verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. other: The payment is pending for a reason other than those listed above. For more information, contact PayPal Customer Service.',
		'protection_eligibility' => 'ExpandedSellerProtection: Seller is protected by Expanded seller protection SellerProtection: Seller is protected by PayPals Seller Protection Policy None: Seller is not protected under Expanded seller protection nor the Seller Protection Policy',
		'quantity' => 'Quantity as entered by your customer or as passed by you, the merchant. If this is a shopping cart transaction, PayPal appends the number of the item (e.g. quantity1, quantity2).',
		'reason_code' => 'This variable is set if payment_status is Reversed, Refunded, Canceled_Reversal, or Denied. adjustment_reversal: Reversal of an adjustment. admin_fraud_reversal: The transaction has been reversed due to fraud detected by PayPal administrators. admin_reversal: The transaction has been reversed by PayPal administrators. buyer-complaint: The transaction has been reversed due to a complaint from your customer. chargeback: The transaction has been reversed due to a chargeback by your customer. chargeback_reimbursement: Reimbursement for a chargeback. chargeback_settlement: Settlement of a chargeback. guarantee: The transaction has been reversed because your customer exercised a money-back guarantee. other: Unspecified reason.  refund: The transaction has been reversed because you gave the customer a refund.  regulatory_block: PayPal  blocked the transaction due to a violation of a government regulation. In this case, payment_status is Denied. regulatory_reject: PayPal  rejected the transaction due to a violation of a government regulation and returned the funds to the buyer. In this case, payment_status is Denied. regulatory_review_exceeding_sla: PayPal did not complete the    review   for compliance with government regulations within 72 hours, as required. Consequently, PayPal auto-reversed the transaction and returned the funds to the buyer. In this case, payment_status is Denied. Note that "sla" stand for "service level agreement". unauthorized_claim: The transaction has been reversed because  it was not authorized by the buyer. unauthorized_spoof: The transaction has been reversed due to a customer dispute in which an unauthorized spoof is suspected.  Note: Additional codes may be returned.',
		'remaining_settle' => 'Remaining amount that can be captured with Authorization and Capture',
		'settle_amount' => 'Amount that is deposited into the accounts primary balance after a currency conversion from automatic conversion (through your Payment Receiving Preferences) or manual conversion (through manually accepting a payment).',
		'settle_currency' => 'Currency of settle_amount.',
		'shipping' => 'Shipping charges associated with this transaction.Format: unsigned, no currency symbol, two decimal places.',
		'shipping_method' => 'The name of a shipping method from the Shipping Calculations section of the merchants account profile. The buyer selected the named shipping method for this transaction.',
		'tax' => 'Amount of tax charged on payment. PayPal appends the number of the item (e.g., item_name1, item_name2). The taxx variable is included only if there was a specific tax amount applied to a particular shopping cart item. Because total tax may apply to other items in the cart, the sum of taxx might not total to tax.',
		'transaction_entity' => 'Authorization and Capture transaction entity'
	];
}

/**
 * gets the paypal fields for pay  vars
 *
 * @return array an array of field name => description
 */
function get_paypal_pay_vars() {
	return [
		'transaction_type' => 'The type of transaction. Possible values are:   Adaptive Payment PAY This notification occurs when is a payment is made due to a Pay Request. The variables for the Adaptive Payment Pay notification are similar to the PaymentDetailsResponse fields.   AdjustmentThis can be for a chargeback, reversal, or refund; check the reason_code to see which it is.',
		'status' => 'The status of the payment. Possible values are:   CANCELED - The Preapproval agreement was cancelled  CREATED - The payment request was received; funds will be transferred once the payment is approved  COMPLETED - The payment was successful  INCOMPLETE - Some transfers succeeded and some failed for a parallel payment or, for a delayed chained payment, secondary receivers have not been paid  ERROR - The payment failed and all attempted transfers failed or all completed transfers were successfully reversed   REVERSALERROR - One or more transfers failed when attempting to reverse a payment  PROCESSING - The payment is in progress  PENDING - The payment is awaiting processing',
		'sender_email' => 'Senders email address.',
		'action_type' => 'Whether the Pay API is used with or without the SetPaymentOptions and ExecutePayment API operations. Possible values are:   PAY - If you are not using the SetPaymentOptions and ExecutePayment API operations   CREATE - If you are using the SetPaymentOptions and ExecutePayment API operations',
		'payment_request_date' => 'The date on which the payment request was initiated.',
		'reverse_all_parallel_payments_on_error' => 'Whether the payment request specified to reverse parallel payments if an error occurs. Possible values are:   true - Each parallel payment is reversed if an error occurs    false - Only incomplete payments are reversed (default)',
		'transaction[n].id' => 'The transaction ID, where [n] is a number from 0 to 5. For simple, single receiver payments, this number will be 0. Numbers larger than 0 indicate the payment to a particular receiver in chained and parallel payments.',
		'transaction[n].status' => 'The transaction status, where [n] is a number from 0 to 5. For simple single-receiver payments, this number will be 0. Numbers larger than 0 indicate the payment to a particular receiver in chained and parallel payments.Possible values are:  Completed  Pending  Refunded',
		'transaction[n].id_for_sender' => 'The transaction ID for the sender, where [n] is a number from 0 to 5. For simple, single receiver payments, this number will be 0. Numbers larger than 0 indicate the payment to a particular receiver in chained and parallel payments.',
		'transaction[n].status_for_sender_txn' => 'The transaction status, where [n] is a number from 0 to 5. For simple single-receiver payments, this number will be 0. Numbers larger than 0 indicate the payment to a particular receiver in chained and parallel payments.Possible values are:   COMPLETED - The senders transaction has completed   PENDING - The transaction is awaiting further processing   CREATED - The payment request was received; funds will be transferred once approval is received   PARTIALLY_REFUNDED - Transaction was partially refunded   DENIED - The transaction was rejected by the receiver   PROCESSING - The transaction is in progress   REVERSED - The payment was returned to the sender   REFUNDED - The payment was refunded   FAILED - The payment failed',
		'transaction[n].refund_id' => 'The identification number for the refund',
		'transaction[n].refund_amount' => 'The amount that was refunded.',
		'transaction[n].refund_account_charged' => 'The email address of the debit account of the refund.',
		'transaction[n].receiver' => 'The receivers email address for the transaction',
		'transaction[n].invoiceId' => 'The invoice number for this transaction',
		'transaction[n].amount' => 'The payment amount of the transaction',
		'transaction[n].is_primary_receiver' => 'Whether there is a primary receiver for this transaction, which indicates whether the transaction is a chained payment. Possible values are:   true - There is a primary receiver (chained payment)   false - There is no primary receiver (simple or parallel payment)',
		'return_url' => 'The URL to which the senders browser is redirected after approving a payment on paypal.com. Use the pay key to identify the payment as follows: payKey=${payKey}.',
		'cancel_url' => 'The URL to which the senders browser is redirected if the sender cancels the approval for a payment on paypal.com. Use the pay key to identify the payment as follows: payKey=${payKey}.',
		'ipn_notification_url' => 'The URL to which all IPN messages for this payment are sent.',
		'pay_key' => 'The pay key that identifies this payment. This is a token that is assigned by the Pay API after a PayRequest message is received and can be used in other Adaptive Payments APIs as well as the cancelURL and returnURL to identify this payment. The pay key is valid for 3 hours.',
		'memo' => 'A note associated with the payment.',
		'fees_payer' => 'The payer of PayPal fees. Possible values are:   SENDER - Sender pays all fees (for personal, implicit simple/parallel payments; do not use for chained or unilateral payments)   PRIMARYRECEIVER - Primary receiver pays all fees (chained payments only)   EACHRECEIVER - Each receiver pays their own fee (default, personal and unilateral payments)   SECONDARYONLY - Secondary receivers pay all fees (use only for chained payments with one secondary receiver)',
		'trackingId' => 'The tracking ID that was specified for this payment in the PaymentDetailsRequest message.',
		'preapproval_key' => 'The preapproval key returned after a PreapprovalRequest, or the preapproval key that identifies the preapproval key sent with a PayRequest.',
		'reason_code' => 'Whether this transaction is a chargeback, partial, or reversal. Possible values are:   Chargeback Settlement - Transaction is a chargeback   Admin reversal - Transaction was reversed by PayPal administrators   Refund - Transaction was partially or fully refunded'
	];
}

/**
 * gets the paypal fields for pdt specific things
 *
 * @return array an array of field name => description
 */
function get_paypal_pdt_specific_vars() {
	return [
		'amt' => 'Amount of the transaction',
		'cc' => 'Currency code',
		'cm' => 'Custom message',
		'sig' => '',
		'st' => 'Transaction status',
		'tx' => 'Transaction ID/PDT token'
	];
}

/**
 * gets the paypal fields for preapproval
 *
 * @return array an array of field name => description
 */
function get_paypal_preapproval_vars() {
	return [
		'transaction_type' => 'The type of transaction. For a preapproval, this variable returns Adaptive Payment Preapproval.  Note: If this variable is set to Adaptive Payment Pay or Adjustment, refer to the Pay Message Variable section.',
		'preapproval_key' => 'The preapproval key returned after a PreapprovalRequest.',
		'approved' => 'Whether the preapproval request was approved. Possible values are:   true - The preapproval was approved   false - The preapproval was denied',
		'cancel_url' => 'The URL to which the senders browser is redirected if the sender decides to cancel the preapproval as requested. Use the preapproval key to identify the payment as follows: preapprovalKey=${preapprovalKey}',
		'current_number_of_payments' => 'The current number of payments made for this preapproval.',
		'current_total_amount_of_all_payments' => 'The current total of payments made for this preapproval.',
		'current_period_attempts' => 'The current number of attempts this period for this preapproval.',
		'currency_code' => 'The currency code. Possible values are:  Australian Dollar - AUD   Brazilian Real - BRL  Note: The Real is supported as a payment currency and currency balance only for Brazilian PayPal accounts.    Canadian Dollar - CAD   Czech Koruna - CZK   Danish Krone - DKK   Euro - EUR   Hong Kong Dollar - HKD   Hungarian Forint - HUF   Israeli New Sheqel - ILS   Japanese Yen - JPY   Malaysian Ringgit - MYR  Note: The Ringgit is supported as a payment currency and currency balance only for Malaysian PayPal accounts.    Mexican Peso - MXN   Norwegian Krone - NOK   New Zealand Dollar - NZD   Philippine Peso - PHP   Polish Zloty - PLN   Pound Sterling - GBP   Singapore Dollar - SGD   Swedish Krona - SEK   Swiss Franc - CHF   Taiwan New Dollar - TWD   Thai Baht - THB   Turkish Lira - TRY  Note: The Turkish Lira is supported as a payment currency and currency balance only for Turkish PayPal accounts.    U.S. Dollar - USD',
		'date_of_month' => 'The day of the month on which a monthly payment is to be made. A number between 1 and 31 indicates the day of the month. A value of 0 indicates that the payment can be made on any day.',
		'day_of_week' => 'The day of the week that a weekly payment is to be made. Possible values are:  NO_DAY_SPECIFIED  SUNDAY  MONDAY  TUESDAY  WEDNESDAY  THURSDAY  FRIDAY  SATURDAY',
		'starting_date' => 'First date for which the preapproval is valid.',
		'ending_date' => 'Last date for which the preapproval is valid. Time is currently not supported.',
		'max_total_amount_of_all_payments' => 'The pre-approved maximum total amount of all payments.',
		'max_amount_per_payment' => 'The pre-approved maximum amount of all payments.',
		'max_number_of_payments' => 'The maximum number of payments that is pre-approved.',
		'payment_period' => 'The payment period. Possible values are:  NO_PERIOD_SPECIFIED  DAILY  WEEKLY  BIWEEKLY  SEMIMONTHLY  MONTHLY  ANNUALLY',
		'pin_type' => 'Whether a personal identification number (PIN) is required. It is one of the following values:   NOT_REQUIRED - A PIN is not required   REQUIRED - A PIN is required',
		'sender_email' => 'The senders email address.'
	];
}

/**
 * gets the paypal fields for recurring payments
 *
 * @return array an array of field name => description
 */
function get_paypal_recurring_payment_vars() {
	return [
		'amount' => 'Amount of recurring payment',
		'amount_per_cycle' => 'Amount of recurring payment per cycle',
		'initial_payment_amount' => 'Initial payment amount for recurring payments',
		'next_payment_date' => 'Next payment date for a recurring payment',
		'outstanding_balance' => 'Outstanding balance for recurring payments',
		'payment_cycle' => 'Payment cycle for recurring payments',
		'period_type' => 'Kind of period for a recurring payment',
		'product_name' => 'Product name associated with a recurring payment',
		'product_type' => 'Product name associated with a recurring payment',
		'profile_status' => 'Profile status for a recurring payment',
		'recurring_payment_id' => 'Recurring payment ID',
		'rp_invoice_id' => 'The merchants own unique reference or invoice number, which can be used to uniquely identify a profile.Length: 127 single-byte alphanumeric characters',
		'time_created' => 'When a recurring payment was created'
	];
}

/**
 * gets the paypal fields for subscriptions
 *
 * @return array an array of field name => description
 */
function get_paypal_subscription_vars() {
	return [
		'amount1' => 'Amount of payment for trial period 1 for USD payments; otherwise blank (optional).',
		'amount2' => 'Amount of payment for trial period 2 for USD payments; otherwise blank (optional).',
		'amount3' => 'Amount of payment for regular subscription period for USD payments; otherwise blank.',
		'mc_amount1' => 'Amount of payment for trial period 1, regardless of currency (optional).',
		'mc_amount2' => 'Amount of payment for trial period 2, regardless of currency (optional).',
		'mc_amount3' => 'Amount of payment for regular subscription period, regardless of currency.',
		'password' => '(optional) Password generated by PayPal and given to subscriber to access the subscription (password will be encrypted).Length: 24 characters',
		'period1' => '(optional) Trial subscription interval in days, weeks, months, years (example: a 4 day interval is "period1: 4 D").',
		'period2' => '(optional) Trial subscription interval in days, weeks, months, or years.',
		'period3' => 'Regular subscription interval in days, weeks, months, or years.',
		'reattempt' => 'Indicates whether reattempts should occur upon payment failures (1 is yes, blank is no).',
		'recur_times' => 'The number of payment installments that will occur at the regular rate.',
		'recurring' => 'Indicates whether regular rate recurs (1 is yes, blank is no).',
		'retry_at' => 'Date PayPal will retry a failed subscription payment.',
		'subscr_date' => 'Start date or cancellation date depending on whether transaction is subscr_signup or subscr_cancel.Time/Date stamp generated by PayPal, in the following format: HH:MM:SS DD Mmm YY, YYYY PST',
		'subscr_effective' => 'Date when the subscription modification will be effective (only for txn_type = subscr_modify).Time/Date stamp generated by PayPal, in the following format: HH:MM:SS DD Mmm YY, YYYY PST',
		'subscr_id' => 'ID generated by PayPal for the subscriber.Length: 19 characters',
		'username' => '(optional) Username generated by PayPal and given to subscriber to access the subscription.Length: 64 characters'
	];
}

/**
 * gets the paypal fields for transaction notifications
 *
 * @return array an array of field name => description
 */
function get_paypal_transaction_notification_vars() {
	return [];
}

/**
 * gets a paypal link url
 *
 * @param string $custom what to put in the custom field, which is used for internal matching
 * @param float $service_cost the cost of the item
 * @param string $description description of the item being charged
 * @return string the link url
 */
function get_paypal_link_url($custom, $service_cost, $description = '') {
	if ($description == '')
		$description = TITLE;
	$description = urlencode($description);
	if (mb_strlen($custom) > 200)
		$custom = 'COMPRESSED'.base64_encode(gzcompress($custom));
	return 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='.PAYPAL.'&item_name='.$description.'&custom='.htmlspecial($custom).'&buyer_credit_promo_code=&buyer_credit_product_category=&buyer_credit_shipping_method=&buyer_credit_user_address_change='.'&amount='.htmlspecial(number_format($service_cost, 2)).'&no_shipping=0&no_note=1&currency_code=USD&lc=US&bn=PP%2dBuyNowBF&charset=UTF%2d8';
}

/**
 * gets a paypal subscription link url
 *
 * @param string $custom what to put in the custom field, which is used for internal matching
 * @param float $service_cost the cost of the item
 * @param string $description description of the item being charged
 * @param int $days_till_first_payment defaults to 1, optional delay before the first payment is sent
 * @return string the link url
 */
function get_paypal_subscription_link_url($custom, $service_cost, $description = '', $days_till_first_payment = 1) {
	if ($description == '')
		$description = TITLE;
	$description = urlencode($description);
	if (mb_strlen($custom) > 200)
		$custom = 'COMPRESSED'.base64_encode(gzcompress($custom));
	return 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business='.htmlspecial(PAYPAL).'&lc=US&item_name='.htmlspecial($description).'&custom='.htmlspecial($custom).'&no_note=1&src=1'.($days_till_first_payment > 1 ? '&a1=0&p1=30&t1=D' : '').'&a3='.htmlspecial(number_format($service_cost, 2)).'&p3='.(int)$days_till_first_payment.'&t3=M&currency_code=USD&bn=PP%2dSubscriptionsBF%3abtn_subscribeCC_LG%2egif%3aNonHostedGuest';
}

/**
 * gets a paypal subscription link
 *
 * @param string $custom the custom field for the link
 * @param float $service_cost the cost of the service
 * @param string $description the description
 * @param string $link_text the text of the link
 * @param int $days_till_first_payment defaults to 1, optional delay before the first payment is sent
 * @return string the a href link
 */
function get_paypal_subscription_link($custom, $service_cost, $description = '', $link_text = 'Setup Paypal Subscription', $days_till_first_payment = 1) {
	$link = '<a href="'.get_paypal_subscription_link_url($custom, $service_cost, $description, $days_till_first_payment).'" target="_blank">'.$link_text.'</a>';
	return $link;
}

/**
 * gets a paypal link
 *
 * @param string $custom the custom field for the link
 * @param float $service_cost the cost of the service
 * @param string $description the description
 * @param string $link_text the text of the link
 * @return string the a href link
 */
function get_paypal_link($custom, $service_cost, $description = '', $link_text = 'Click Here to make a PayPal payment') {
	return '<a href="'.get_paypal_link_url($custom, $service_cost, $description).'" target=_blank>'.$link_text.'</a>';
}

/**
* determines if a paypal transaction has been refunded or not
*
* @param string $txn_id paypal transaction id
* @param string $module module name
* @return bool whether or not it was refunded
*/
function is_paypal_txn_refunded($txn_id, $module = 'default') {
	$module = get_module_name($module);
	$refunded = false;
	$settings = get_module_settings($module);
	$db = get_module_db($module);
	$safe = $db->real_escape($txn_id);
	$db->query("select * from paypal where payment_status='Refunded' and parent_txn_id='{$safe}';", __LINE__, __FILE__);
	if ($db->num_rows() > 0)
		$refunded = true;
	return $refunded;
}
