<?php
	/**
	 * Administrative Functionality
	 * @author Joe Huss <detain@interserver.net>
	 * @copyright 2018
	 * @package MyAdmin
	 * @category Admin
	 */

	/**
	 * paypal_history()
	 *
	 */
	function paypal_history() {
		page_title('PayPal History');
		function_requirements('has_acl');
		if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
			dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
			return FALSE;
		}
		add_output(render_form('paypal_history', ['module' => $GLOBALS['tf']->variables->request['module']]));
	}
