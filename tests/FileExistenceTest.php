<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests that verify the existence and basic validity of all package source files.
 *
 * Ensures no files are accidentally missing or misnamed after refactoring.
 */
class FileExistenceTest extends TestCase
{
    /**
     * @var string Base path to the package root directory.
     */
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = dirname(__DIR__);
    }

    /**
     * Tests that the main Plugin.php source file exists.
     *
     * @return void
     */
    public function testPluginPhpExists(): void
    {
        $this->assertFileExists($this->basePath . '/src/Plugin.php');
    }

    /**
     * Tests that the PayPalCheckout.php source file exists.
     *
     * @return void
     */
    public function testPayPalCheckoutPhpExists(): void
    {
        $this->assertFileExists($this->basePath . '/src/PayPalCheckout.php');
    }

    /**
     * Tests that the paypal functions include file exists.
     *
     * @return void
     */
    public function testPaypalFunctionsIncFileExists(): void
    {
        $this->assertFileExists($this->basePath . '/src/paypal.functions.inc.php');
    }

    /**
     * Tests that the paypal refund functions file exists.
     *
     * @return void
     */
    public function testPaypalRefundFunctionsFileExists(): void
    {
        $this->assertFileExists($this->basePath . '/src/paypal_refund.functions.php');
    }

    /**
     * Tests that all admin page files exist.
     *
     * @dataProvider adminFilesProvider
     * @param string $filename
     * @return void
     */
    public function testAdminFileExists(string $filename): void
    {
        $this->assertFileExists($this->basePath . '/src/admin/' . $filename);
    }

    /**
     * Data provider for admin files.
     *
     * @return array<string, array{0: string}>
     */
    public function adminFilesProvider(): array
    {
        return [
            'paypal_history' => ['paypal_history.php'],
            'paypal_refund' => ['paypal_refund.php'],
            'paypal_transactions' => ['paypal_transactions.php'],
            'view_paypal_transaction' => ['view_paypal_transaction.php'],
        ];
    }

    /**
     * Tests that all JSON info files exist.
     *
     * @dataProvider jsonInfoFilesProvider
     * @param string $filename
     * @return void
     */
    public function testJsonInfoFileExists(string $filename): void
    {
        $this->assertFileExists($this->basePath . '/src/info/' . $filename);
    }

    /**
     * Data provider for JSON info files.
     *
     * @return array<string, array{0: string}>
     */
    public function jsonInfoFilesProvider(): array
    {
        return [
            'adaptive_accounts_ipn' => ['paypal_adaptive_accounts_ipn_messages.json'],
            'buyer_info' => ['paypal_buyer_info_variables.json'],
            'dispute_resolution' => ['paypal_dispute_resolutoin_variables.json'],
            'masspay' => ['paypal_masspay_variables.json'],
            'pay_message' => ['paypal_pay_message_variables.json'],
            'payment_info' => ['paypal_payment_info_variables.json'],
            'pdt_specific' => ['paypal_pdt_specific_variables.json'],
            'preapproval' => ['paypal_preapproval_message_variables.json'],
            'recurring_payment' => ['paypal_recurring_payment_variables.json'],
            'subscription' => ['paypal_subscription_variables.json'],
            'transaction_notification' => ['paypal_transaction_notifcation_variables.json'],
            'transaction_types' => ['paypal_transaction_types.json'],
        ];
    }

    /**
     * Tests that all JSON info files contain valid JSON.
     *
     * @dataProvider jsonInfoFilesProvider
     * @param string $filename
     * @return void
     */
    public function testJsonInfoFileContainsValidJson(string $filename): void
    {
        $path = $this->basePath . '/src/info/' . $filename;
        $content = file_get_contents($path);
        $this->assertNotFalse($content, "Should be able to read {$filename}");

        $decoded = json_decode($content, true);
        $this->assertNotNull($decoded, "File {$filename} should contain valid JSON: " . json_last_error_msg());
    }

    /**
     * Tests that the composer.json file exists.
     *
     * @return void
     */
    public function testComposerJsonExists(): void
    {
        $this->assertFileExists($this->basePath . '/composer.json');
    }

    /**
     * Tests that composer.json contains valid JSON.
     *
     * @return void
     */
    public function testComposerJsonIsValid(): void
    {
        $content = file_get_contents($this->basePath . '/composer.json');
        $this->assertNotFalse($content);

        $decoded = json_decode($content, true);
        $this->assertNotNull($decoded, 'composer.json should contain valid JSON');
        $this->assertArrayHasKey('name', $decoded);
        $this->assertSame('detain/myadmin-paypal-payments', $decoded['name']);
    }

    /**
     * Tests that the README file exists.
     *
     * @return void
     */
    public function testReadmeExists(): void
    {
        $this->assertFileExists($this->basePath . '/README.md');
    }

    /**
     * Tests that all PHP source files are readable and start with PHP opening tag.
     *
     * @dataProvider phpSourceFilesProvider
     * @param string $relativePath
     * @return void
     */
    public function testPhpFileIsReadableAndValid(string $relativePath): void
    {
        $fullPath = $this->basePath . '/' . $relativePath;
        $this->assertFileExists($fullPath);
        $this->assertFileIsReadable($fullPath);

        $content = file_get_contents($fullPath);
        $this->assertNotFalse($content);
        $this->assertStringStartsWith('<?php', ltrim($content), "File {$relativePath} should start with PHP opening tag");
    }

    /**
     * Data provider for PHP source files.
     *
     * @return array<string, array{0: string}>
     */
    public function phpSourceFilesProvider(): array
    {
        return [
            'Plugin' => ['src/Plugin.php'],
            'PayPalCheckout' => ['src/PayPalCheckout.php'],
            'paypal_functions' => ['src/paypal.functions.inc.php'],
            'paypal_refund_functions' => ['src/paypal_refund.functions.php'],
            'admin_paypal_history' => ['src/admin/paypal_history.php'],
            'admin_paypal_refund' => ['src/admin/paypal_refund.php'],
            'admin_paypal_transactions' => ['src/admin/paypal_transactions.php'],
            'admin_view_paypal_transaction' => ['src/admin/view_paypal_transaction.php'],
        ];
    }
}
