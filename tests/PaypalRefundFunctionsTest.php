<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;

/**
 * Tests for the functions defined in paypal_refund.functions.php.
 *
 * Validates function existence, parameter signatures, and structure
 * without invoking external API calls or database operations.
 */
class PaypalRefundFunctionsTest extends TestCase
{
    /**
     * Ensures the refund functions file is loaded.
     */
    public static function setUpBeforeClass(): void
    {
        // Define required constants if not already defined
        if (!defined('PAYPAL_API_USERNAME')) {
            define('PAYPAL_API_USERNAME', 'test_user');
        }
        if (!defined('PAYPAL_API_PASSWORD')) {
            define('PAYPAL_API_PASSWORD', 'test_pass');
        }
        if (!defined('PAYPAL_API_SIGNATURE')) {
            define('PAYPAL_API_SIGNATURE', 'test_sig');
        }

        $file = dirname(__DIR__) . '/src/paypal_refund.functions.php';
        if (!function_exists('PayPalHttpPost')) {
            require_once $file;
        }
    }

    /**
     * Tests that PayPalHttpPost function exists.
     *
     * @return void
     */
    public function testPayPalHttpPostFunctionExists(): void
    {
        $this->assertTrue(function_exists('PayPalHttpPost'));
    }

    /**
     * Tests that refundPaypalTransaction function exists.
     *
     * @return void
     */
    public function testRefundPaypalTransactionFunctionExists(): void
    {
        $this->assertTrue(function_exists('refundPaypalTransaction'));
    }

    /**
     * Tests that PayPalHttpPost has the expected parameter signature.
     *
     * @return void
     */
    public function testPayPalHttpPostParameterSignature(): void
    {
        $ref = new ReflectionFunction('PayPalHttpPost');
        $params = $ref->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('methodName_', $params[0]->getName());
        $this->assertSame('nvpStr_', $params[1]->getName());
        $this->assertSame('env', $params[2]->getName());
        $this->assertTrue($params[2]->isOptional());
        $this->assertSame('live', $params[2]->getDefaultValue());
    }

    /**
     * Tests that refundPaypalTransaction has the expected parameter signature.
     *
     * @return void
     */
    public function testRefundPaypalTransactionParameterSignature(): void
    {
        $ref = new ReflectionFunction('refundPaypalTransaction');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('transactionId', $params[0]->getName());
        $this->assertTrue($params[0]->isOptional());
        $this->assertNull($params[0]->getDefaultValue());
    }

    /**
     * Tests that refundPaypalTransaction returns failure when called with null.
     *
     * @return void
     */
    public function testRefundPaypalTransactionReturnsFailureForNull(): void
    {
        $result = refundPaypalTransaction(null);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertSame('Failed', $result['status']);
        $this->assertArrayHasKey('msg', $result);
        $this->assertStringContainsString('empty', strtolower($result['msg']));
    }

    /**
     * Tests that refundPaypalTransaction returns failure when called with no arguments.
     *
     * @return void
     */
    public function testRefundPaypalTransactionReturnsFailureForDefault(): void
    {
        $result = refundPaypalTransaction();

        $this->assertIsArray($result);
        $this->assertSame('Failed', $result['status']);
    }
}
