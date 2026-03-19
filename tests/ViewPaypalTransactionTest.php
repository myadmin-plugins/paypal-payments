<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the functions defined in view_paypal_transaction.php.
 *
 * Validates the get_paypal_transaction_types() and get_paypal_cats_and_fields()
 * functions return properly structured data.
 */
class ViewPaypalTransactionTest extends TestCase
{
    /**
     * Ensures the view_paypal_transaction file is loaded.
     */
    public static function setUpBeforeClass(): void
    {
        $file = dirname(__DIR__) . '/src/admin/view_paypal_transaction.php';
        if (!function_exists('get_paypal_transaction_types')) {
            require_once $file;
        }
    }

    /**
     * Tests that get_paypal_transaction_types returns an array.
     *
     * @return void
     */
    public function testGetPaypalTransactionTypesReturnsArray(): void
    {
        $result = get_paypal_transaction_types();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that transaction types have string keys and string values.
     *
     * @return void
     */
    public function testTransactionTypesHaveStringKeysAndValues(): void
    {
        $result = get_paypal_transaction_types();
        foreach ($result as $key => $value) {
            $this->assertIsString($key, 'Transaction type key should be a string');
            $this->assertIsString($value, "Description for type '{$key}' should be a string");
        }
    }

    /**
     * Tests that transaction types contain common PayPal transaction types.
     *
     * @return void
     */
    public function testTransactionTypesContainsCommonTypes(): void
    {
        $result = get_paypal_transaction_types();

        $expectedKeys = [
            'cart',
            'express_checkout',
            'web_accept',
            'subscr_signup',
            'subscr_payment',
            'subscr_cancel',
            'recurring_payment',
            'send_money',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Should contain transaction type '{$key}'");
        }
    }

    /**
     * Tests that get_paypal_cats_and_fields function exists.
     *
     * @return void
     */
    public function testGetPaypalCatsAndFieldsFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_paypal_cats_and_fields'));
    }

    /**
     * Tests that get_paypal_cats_and_fields returns an array.
     *
     * @return void
     */
    public function testGetPaypalCatsAndFieldsReturnsArray(): void
    {
        $result = get_paypal_cats_and_fields();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that cats and fields entries have required structure with name, desc, and fields keys.
     *
     * @return void
     */
    public function testCatsAndFieldsEntriesHaveRequiredStructure(): void
    {
        $result = get_paypal_cats_and_fields();
        foreach ($result as $index => $category) {
            $this->assertIsArray($category, "Entry at index {$index} should be an array");
            $this->assertArrayHasKey('name', $category, "Entry at index {$index} should have a 'name' key");
            $this->assertArrayHasKey('fields', $category, "Entry at index {$index} should have a 'fields' key");
            $this->assertIsString($category['name'], "Name at index {$index} should be a string");
            $this->assertIsArray($category['fields'], "Fields at index {$index} should be an array");
        }
    }
}
