<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the procedural functions in paypal.functions.inc.php.
 *
 * Validates that all getter functions return properly structured arrays
 * and that link URL generation functions exist with correct signatures.
 */
class PaypalFunctionsTest extends TestCase
{
    /**
     * Ensures the functions file is loaded before tests run.
     */
    public static function setUpBeforeClass(): void
    {
        $file = dirname(__DIR__) . '/src/paypal.functions.inc.php';
        if (!function_exists('get_paypal_adaptive_accounts_ipn_messages')) {
            require_once $file;
        }
    }

    // --- Adaptive Accounts IPN Messages ---

    /**
     * Tests that get_paypal_adaptive_accounts_ipn_messages returns an array.
     *
     * @return void
     */
    public function testGetPaypalAdaptiveAccountsIpnMessagesReturnsArray(): void
    {
        $result = get_paypal_adaptive_accounts_ipn_messages();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that adaptive accounts fields have string keys and string values.
     *
     * @return void
     */
    public function testAdaptiveAccountsFieldsAreStringMapped(): void
    {
        $result = get_paypal_adaptive_accounts_ipn_messages();
        foreach ($result as $key => $value) {
            $this->assertIsString($key, 'Field name should be a string');
            $this->assertIsString($value, "Description for '{$key}' should be a string");
        }
    }

    /**
     * Tests that adaptive accounts contains expected keys.
     *
     * @return void
     */
    public function testAdaptiveAccountsContainsExpectedKeys(): void
    {
        $result = get_paypal_adaptive_accounts_ipn_messages();
        $this->assertArrayHasKey('notify_version', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('verify_sign', $result);
        $this->assertArrayHasKey('event_type', $result);
    }

    // --- Auction Vars ---

    /**
     * Tests that get_paypal_auction_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalAuctionVarsReturnsArray(): void
    {
        $result = get_paypal_auction_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that auction vars contain expected keys.
     *
     * @return void
     */
    public function testAuctionVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_auction_vars();
        $this->assertArrayHasKey('auction_buyer_id', $result);
        $this->assertArrayHasKey('for_auction', $result);
    }

    // --- Buyer Information Vars ---

    /**
     * Tests that get_paypal_buyer_information_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalBuyerInformationVarsReturnsArray(): void
    {
        $result = get_paypal_buyer_information_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that buyer info vars contain address-related keys.
     *
     * @return void
     */
    public function testBuyerInformationVarsContainsAddressKeys(): void
    {
        $result = get_paypal_buyer_information_vars();
        $this->assertArrayHasKey('address_country', $result);
        $this->assertArrayHasKey('address_city', $result);
        $this->assertArrayHasKey('address_state', $result);
        $this->assertArrayHasKey('address_street', $result);
        $this->assertArrayHasKey('address_zip', $result);
        $this->assertArrayHasKey('payer_email', $result);
        $this->assertArrayHasKey('payer_id', $result);
    }

    // --- Dispute Resolution Vars ---

    /**
     * Tests that get_paypal_dispute_resolution_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalDisputeResolutionVarsReturnsArray(): void
    {
        $result = get_paypal_dispute_resolution_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that dispute resolution vars contain expected keys.
     *
     * @return void
     */
    public function testDisputeResolutionVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_dispute_resolution_vars();
        $this->assertArrayHasKey('case_id', $result);
        $this->assertArrayHasKey('case_type', $result);
        $this->assertArrayHasKey('reason_code', $result);
    }

    // --- Global Shipping Vars ---

    /**
     * Tests that get_paypal_global_shipping_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalGlobalShippingVarsReturnsArray(): void
    {
        $result = get_paypal_global_shipping_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that shipping vars contain fulfillment address keys.
     *
     * @return void
     */
    public function testGlobalShippingVarsContainsFulfillmentKeys(): void
    {
        $result = get_paypal_global_shipping_vars();
        $this->assertArrayHasKey('fulfillment_address_country', $result);
        $this->assertArrayHasKey('fulfillment_address_city', $result);
        $this->assertArrayHasKey('fulfillment_address_zip', $result);
    }

    // --- Mass Pay Vars ---

    /**
     * Tests that get_paypal_mass_pay_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalMassPayVarsReturnsArray(): void
    {
        $result = get_paypal_mass_pay_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that mass pay vars contain expected keys.
     *
     * @return void
     */
    public function testMassPayVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_mass_pay_vars();
        $this->assertArrayHasKey('masspay_txn_id_x', $result);
        $this->assertArrayHasKey('payment_status', $result);
    }

    // --- Payment Information Vars ---

    /**
     * Tests that get_paypal_payment_information_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalPaymentInformationVarsReturnsArray(): void
    {
        $result = get_paypal_payment_information_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that payment info vars contain core payment keys.
     *
     * @return void
     */
    public function testPaymentInformationVarsContainsCoreKeys(): void
    {
        $result = get_paypal_payment_information_vars();
        $this->assertArrayHasKey('payment_status', $result);
        $this->assertArrayHasKey('payment_type', $result);
        $this->assertArrayHasKey('mc_gross', $result);
        $this->assertArrayHasKey('mc_fee', $result);
        $this->assertArrayHasKey('mc_currency', $result);
    }

    // --- Pay Vars ---

    /**
     * Tests that get_paypal_pay_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalPayVarsReturnsArray(): void
    {
        $result = get_paypal_pay_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that pay vars contain expected keys.
     *
     * @return void
     */
    public function testPayVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_pay_vars();
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('pay_key', $result);
        $this->assertArrayHasKey('sender_email', $result);
    }

    // --- PDT Specific Vars ---

    /**
     * Tests that get_paypal_pdt_specific_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalPdtSpecificVarsReturnsArray(): void
    {
        $result = get_paypal_pdt_specific_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that PDT vars contain expected keys.
     *
     * @return void
     */
    public function testPdtSpecificVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_pdt_specific_vars();
        $this->assertArrayHasKey('amt', $result);
        $this->assertArrayHasKey('st', $result);
        $this->assertArrayHasKey('tx', $result);
    }

    // --- Preapproval Vars ---

    /**
     * Tests that get_paypal_preapproval_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalPreapprovalVarsReturnsArray(): void
    {
        $result = get_paypal_preapproval_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that preapproval vars contain expected keys.
     *
     * @return void
     */
    public function testPreapprovalVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_preapproval_vars();
        $this->assertArrayHasKey('preapproval_key', $result);
        $this->assertArrayHasKey('approved', $result);
        $this->assertArrayHasKey('sender_email', $result);
    }

    // --- Recurring Payment Vars ---

    /**
     * Tests that get_paypal_recurring_payment_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalRecurringPaymentVarsReturnsArray(): void
    {
        $result = get_paypal_recurring_payment_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that recurring payment vars contain expected keys.
     *
     * @return void
     */
    public function testRecurringPaymentVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_recurring_payment_vars();
        $this->assertArrayHasKey('recurring_payment_id', $result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('profile_status', $result);
    }

    // --- Subscription Vars ---

    /**
     * Tests that get_paypal_subscription_vars returns a non-empty array.
     *
     * @return void
     */
    public function testGetPaypalSubscriptionVarsReturnsArray(): void
    {
        $result = get_paypal_subscription_vars();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests that subscription vars contain expected keys.
     *
     * @return void
     */
    public function testSubscriptionVarsContainsExpectedKeys(): void
    {
        $result = get_paypal_subscription_vars();
        $this->assertArrayHasKey('subscr_id', $result);
        $this->assertArrayHasKey('recurring', $result);
        $this->assertArrayHasKey('amount3', $result);
    }

    // --- Transaction Notification Vars ---

    /**
     * Tests that get_paypal_transaction_notification_vars returns an array.
     *
     * @return void
     */
    public function testGetPaypalTransactionNotificationVarsReturnsArray(): void
    {
        $result = get_paypal_transaction_notification_vars();
        $this->assertIsArray($result);
    }

    // --- All getter consistency tests ---

    /**
     * Tests that all getter functions return arrays with only string keys and string values.
     *
     * @dataProvider getterFunctionProvider
     * @param string $functionName
     * @return void
     */
    public function testGetterFunctionReturnsStringMappedArray(string $functionName): void
    {
        $result = $functionName();
        $this->assertIsArray($result);
        foreach ($result as $key => $value) {
            $this->assertIsString($key, "Key in {$functionName}() should be a string");
            $this->assertIsString($value, "Value for '{$key}' in {$functionName}() should be a string");
        }
    }

    /**
     * Data provider for getter function tests.
     *
     * @return array<string, array{0: string}>
     */
    public function getterFunctionProvider(): array
    {
        return [
            'adaptive_accounts' => ['get_paypal_adaptive_accounts_ipn_messages'],
            'auction' => ['get_paypal_auction_vars'],
            'buyer_info' => ['get_paypal_buyer_information_vars'],
            'dispute_resolution' => ['get_paypal_dispute_resolution_vars'],
            'global_shipping' => ['get_paypal_global_shipping_vars'],
            'mass_pay' => ['get_paypal_mass_pay_vars'],
            'payment_info' => ['get_paypal_payment_information_vars'],
            'pay' => ['get_paypal_pay_vars'],
            'pdt_specific' => ['get_paypal_pdt_specific_vars'],
            'preapproval' => ['get_paypal_preapproval_vars'],
            'recurring_payment' => ['get_paypal_recurring_payment_vars'],
            'subscription' => ['get_paypal_subscription_vars'],
            'transaction_notification' => ['get_paypal_transaction_notification_vars'],
        ];
    }

    // --- Function existence checks for link/URL functions ---

    /**
     * Tests that get_paypal_link_url function exists.
     *
     * @return void
     */
    public function testGetPaypalLinkUrlFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_paypal_link_url'));
    }

    /**
     * Tests that get_paypal_subscription_link_url function exists.
     *
     * @return void
     */
    public function testGetPaypalSubscriptionLinkUrlFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_paypal_subscription_link_url'));
    }

    /**
     * Tests that get_paypal_link function exists.
     *
     * @return void
     */
    public function testGetPaypalLinkFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_paypal_link'));
    }

    /**
     * Tests that get_paypal_subscription_link function exists.
     *
     * @return void
     */
    public function testGetPaypalSubscriptionLinkFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_paypal_subscription_link'));
    }

    /**
     * Tests that is_paypal_txn_refunded function exists.
     *
     * @return void
     */
    public function testIsPaypalTxnRefundedFunctionExists(): void
    {
        $this->assertTrue(function_exists('is_paypal_txn_refunded'));
    }

    // --- Function signature validation ---

    /**
     * Tests that get_paypal_link_url has the expected parameters.
     *
     * @return void
     */
    public function testGetPaypalLinkUrlParameterSignature(): void
    {
        $ref = new \ReflectionFunction('get_paypal_link_url');
        $params = $ref->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertSame('custom', $params[0]->getName());
        $this->assertSame('service_cost', $params[1]->getName());
    }

    /**
     * Tests that get_paypal_subscription_link_url has the expected parameters.
     *
     * @return void
     */
    public function testGetPaypalSubscriptionLinkUrlParameterSignature(): void
    {
        $ref = new \ReflectionFunction('get_paypal_subscription_link_url');
        $params = $ref->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertSame('custom', $params[0]->getName());
        $this->assertSame('service_cost', $params[1]->getName());
    }

    /**
     * Tests that get_paypal_link has the expected parameters.
     *
     * @return void
     */
    public function testGetPaypalLinkParameterSignature(): void
    {
        $ref = new \ReflectionFunction('get_paypal_link');
        $params = $ref->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertSame('custom', $params[0]->getName());
        $this->assertSame('service_cost', $params[1]->getName());
    }

    /**
     * Tests that get_paypal_subscription_link has the expected parameters.
     *
     * @return void
     */
    public function testGetPaypalSubscriptionLinkParameterSignature(): void
    {
        $ref = new \ReflectionFunction('get_paypal_subscription_link');
        $params = $ref->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertSame('custom', $params[0]->getName());
        $this->assertSame('service_cost', $params[1]->getName());
    }
}
