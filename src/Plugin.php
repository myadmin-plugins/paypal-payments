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
	public static $description = 'Allows handling of Paypal emails and honeypots';
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
		$loader->add_requirement('class.Paypal', '/../vendor/detain/myadmin-paypal-payments/src/Paypal.php');
		$loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-paypal-payments/src/abuse.inc.php');
		$loader->add_requirement('deactivate_abuse', '/../vendor/detain/myadmin-paypal-payments/src/abuse.inc.php');
		$loader->add_requirement('get_abuse_licenses', '/../vendor/detain/myadmin-paypal-payments/src/abuse.inc.php');
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
