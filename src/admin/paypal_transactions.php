<?php
	/**
	 * Administrative Functionality
	 * @author Joe Huss <detain@interserver.net>
	 * @copyright 2017
	 * @package MyAdmin
	 * @category Admin
	 */

	/**
	 * paypal_transactions()
	 *
	 */
	function paypal_transactions() {
		page_title('PayPal Transactions');
		function_requirements('has_acl');
		if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
			dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
			return FALSE;
		}
		add_output(render_form('paypal_transactions', ['module' => $GLOBALS['tf']->variables->request['module'], 'acl' => 'client_billing']));
	}
