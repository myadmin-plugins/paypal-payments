<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap file.
 *
 * Creates stub files and defines constants required by the source code
 * so that tests can run without the full MyAdmin application.
 */

// Create the config stub that PayPalCheckout.php tries to include
$configDir = dirname(__DIR__) . '/src/../../../../include/config';
if (!is_dir($configDir)) {
    @mkdir($configDir, 0755, true);
}
$configFile = $configDir . '/config.settings.php';
if (!file_exists($configFile)) {
    file_put_contents($configFile, "<?php\n// Stub for testing\n");
}

// Define constants that the source code references
if (!defined('PAYPAL_ENABLE')) {
    define('PAYPAL_ENABLE', true);
}
if (!defined('PAYPAL_DIGITALGOODS_ENABLE')) {
    define('PAYPAL_DIGITALGOODS_ENABLE', true);
}
if (!defined('PAYPAL_EMAIL')) {
    define('PAYPAL_EMAIL', 'test@example.com');
}
if (!defined('PAYPAL_CLIENT_ID')) {
    define('PAYPAL_CLIENT_ID', 'test_client_id');
}
if (!defined('PAYPAL_SECRET')) {
    define('PAYPAL_SECRET', 'test_secret');
}
if (!defined('PAYPAL_CLIENT_ID_NEW')) {
    define('PAYPAL_CLIENT_ID_NEW', 'test_client_id_new');
}
if (!defined('PAYPAL_SECRET_NEW')) {
    define('PAYPAL_SECRET_NEW', 'test_secret_new');
}
if (!defined('PAYPAL_NVP_WEBHOOK_ID')) {
    define('PAYPAL_NVP_WEBHOOK_ID', 'test_webhook_id');
}
if (!defined('PAYPAL_NVP_CLIENT_ID')) {
    define('PAYPAL_NVP_CLIENT_ID', 'test_nvp_client_id');
}
if (!defined('PAYPAL_NVP_SECRET')) {
    define('PAYPAL_NVP_SECRET', 'test_nvp_secret');
}
if (!defined('PAYPAL_API_USERNAME')) {
    define('PAYPAL_API_USERNAME', 'test_api_user');
}
if (!defined('PAYPAL_API_PASSWORD')) {
    define('PAYPAL_API_PASSWORD', 'test_api_pass');
}
if (!defined('PAYPAL_API_SIGNATURE')) {
    define('PAYPAL_API_SIGNATURE', 'test_api_sig');
}
if (!defined('PAYPAL_SANDBOX_API_USERNAME')) {
    define('PAYPAL_SANDBOX_API_USERNAME', 'test_sandbox_user');
}
if (!defined('PAYPAL_SANDBOX_API_PASSWORD')) {
    define('PAYPAL_SANDBOX_API_PASSWORD', 'test_sandbox_pass');
}
if (!defined('PAYPAL_SANDBOX_API_SIGNATURE')) {
    define('PAYPAL_SANDBOX_API_SIGNATURE', 'test_sandbox_sig');
}

// Load composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';
