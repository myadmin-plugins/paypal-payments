<?php

namespace Detain\MyAdminPaypal;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminPaypal
 */
class Plugin {

	public static $name = 'Paypal Plugin';
	public static $description = 'Allows handling of Paypal based Payments through their Payment Processor/Payment System.';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	public static function getHooks() {
		return [
			'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
			//'function.requirements' => [__CLASS__, 'getRequirements']
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event) {
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
					if (has_acl('client_billing'))
							$menu->add_link('admin', 'choice=none.abuse_admin', '//my.interserver.net/bower_components/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', 'Paypal');
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event) {
		$loader = $event->getSubject();
		$loader->add_requirement('view_paypal_transaction', '/../vendor/detain/myadmin-paypal-payments/src/admin/view_paypal_transaction.php');
		$loader->add_requirement('paypal_history', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_history.php');
		$loader->add_requirement('paypal_transactions', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_transactions.php');
		$loader->add_requirement('paypal_refund', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_refund.php');
		$loader->add_requirement('SetSubscriptionExpressCheckout', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('CreateRecurringPaymentsProfile', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('SetExpressCheckoutDG', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('SetExpressCheckout', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('CallShortcutExpressCheckout', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('CallMarkExpressCheckout', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('GetExpressCheckoutDetails', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('ConfirmPayment', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('DirectPayment', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('paypal_hash_call', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('RedirectToPayPal', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('RedirectToPayPalDG', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('deformatNVP', '/../vendor/detain/myadmin-paypal-payments/src/paypal_checkout.functions.php');
		$loader->add_requirement('get_paypal_adaptive_accounts_ipn_messages', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_auction_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_buyer_information_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_dispute_resolution_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_global_shipping_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_mass_pay_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_payment_information_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_pay_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_pdt_specific_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_preapproval_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_recurring_payment_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_subscription_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_transaction_notification_vars', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_link_url', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_subscription_link_url', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_subscription_link', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('get_paypal_link', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('is_paypal_txn_refunded', '/../vendor/detain/myadmin-paypal-payments/src/paypal.functions.inc.php');
		$loader->add_requirement('PPHttpPost', '/../vendor/detain/myadmin-paypal-payments/src/paypal_refund.functions.php');
		$loader->add_requirement('refundPaypalTransaction', '/../vendor/detain/myadmin-paypal-payments/src/paypal_refund.functions.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_radio_setting('Billing', 'PayPal', 'paypal_enable', 'Enable PayPal', 'Enable PayPal', PAYPAL_ENABLE, [true, false], ['Enabled', 'Disabled']);
		$settings->add_radio_setting('Billing', 'PayPal', 'paypal_digitalgoods_enable', 'Enable Digital Goods', 'Enable Digital Goods', PAYPAL_DIGITALGOODS_ENABLE, [true, false], ['Enabled', 'Disabled']);
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_email', 'Login / Email ', 'Login / Email ', (defined('PAYPAL_EMAIL') ? PAYPAL_EMAIL : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_api_username', 'API Username', 'API Username', (defined('PAYPAL_API_USERNAME') ? PAYPAL_API_USERNAME : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_api_password', 'API Password', 'API Password', (defined('PAYPAL_API_PASSWORD') ? PAYPAL_API_PASSWORD : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_api_signature', 'API Signature', 'API Signature', (defined('PAYPAL_API_SIGNATURE') ? PAYPAL_API_SIGNATURE : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_sandbox_api_username', 'Sandbox API Username', 'Sandbox API Username', (defined('PAYPAL_SANDBOX_API_USERNAME') ? PAYPAL_SANDBOX_API_USERNAME : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_sandbox_api_password', 'Sandbox API Password', 'Sandbox API Password', (defined('PAYPAL_SANDBOX_API_PASSWORD') ? PAYPAL_SANDBOX_API_PASSWORD : ''));
		$settings->add_text_setting('Billing', 'PayPal', 'paypal_sandbox_api_signature', 'Sandbox API Signature', 'Sandbox API Signature', (defined('PAYPAL_SANDBOX_API_SIGNATURE') ? PAYPAL_SANDBOX_API_SIGNATURE : ''));
	}

}
