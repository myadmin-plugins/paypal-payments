<?php

namespace Detain\MyAdminPaypal;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminPaypal
 */
class Plugin
{
	public static $name = 'Paypal Plugin';
	public static $description = 'Allows handling of Paypal based Payments through their Payment Processor/Payment System.';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @return array
	 */
	public static function getHooks()
	{
		return [
			'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
			'function.requirements' => [__CLASS__, 'getRequirements']
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event)
	{
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
			if (has_acl('client_billing')) {
				$menu->add_link('admin', 'choice=none.abuse_admin', '/lib/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', __('Paypal'));
			}
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event)
	{
        /**
         * @var \MyAdmin\Plugins\Loader $this->loader
         */
        $loader = $event->getSubject();
		$loader->add_page_requirement('view_paypal_transaction', '/../vendor/detain/myadmin-paypal-payments/src/admin/view_paypal_transaction.php');
		$loader->add_page_requirement('paypal_history', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_history.php');
		$loader->add_page_requirement('paypal_transactions', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_transactions.php');
		$loader->add_page_requirement('paypal_refund', '/../vendor/detain/myadmin-paypal-payments/src/admin/paypal_refund.php');
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
		$loader->add_requirement('PayPalHttpPost', '/../vendor/detain/myadmin-paypal-payments/src/paypal_refund.functions.php');
		$loader->add_page_requirement('refundPaypalTransaction', '/../vendor/detain/myadmin-paypal-payments/src/paypal_refund.functions.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
    public static function getSettings(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Settings $settings
         **/
        $settings = $event->getSubject();
		$settings->add_radio_setting(__('Billing'), __('PayPal'), 'paypal_enable', __('Enable PayPal'), __('Enable PayPal'), PAYPAL_ENABLE, [true, false], ['Enabled', 'Disabled']);
		$settings->add_radio_setting(__('Billing'), __('PayPal'), 'paypal_digitalgoods_enable', __('Enable Digital Goods'), __('Enable Digital Goods'), PAYPAL_DIGITALGOODS_ENABLE, [true, false], ['Enabled', 'Disabled']);
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_email', __('Login / Email '), __('Login / Email '), (defined('PAYPAL_EMAIL') ? PAYPAL_EMAIL : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_api_username', __('API Username'), __('API Username'), (defined('PAYPAL_API_USERNAME') ? PAYPAL_API_USERNAME : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_api_password', __('API Password'), __('API Password'), (defined('PAYPAL_API_PASSWORD') ? PAYPAL_API_PASSWORD : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_api_signature', __('API Signature'), __('API Signature'), (defined('PAYPAL_API_SIGNATURE') ? PAYPAL_API_SIGNATURE : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_sandbox_api_username', __('Sandbox API Username'), __('Sandbox API Username'), (defined('PAYPAL_SANDBOX_API_USERNAME') ? PAYPAL_SANDBOX_API_USERNAME : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_sandbox_api_password', __('Sandbox API Password'), __('Sandbox API Password'), (defined('PAYPAL_SANDBOX_API_PASSWORD') ? PAYPAL_SANDBOX_API_PASSWORD : ''));
		$settings->add_text_setting(__('Billing'), __('PayPal'), 'paypal_sandbox_api_signature', __('Sandbox API Signature'), __('Sandbox API Signature'), (defined('PAYPAL_SANDBOX_API_SIGNATURE') ? PAYPAL_SANDBOX_API_SIGNATURE : ''));
	}
}
