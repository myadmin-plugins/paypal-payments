<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use Detain\MyAdminPaypal\PayPalCheckout;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Tests for the PayPalCheckout class.
 *
 * Validates class structure, static properties, URL-building methods,
 * sandbox/live endpoint switching, and the NVP deformat parser.
 */
class PayPalCheckoutTest extends TestCase
{
    /**
     * @var ReflectionClass<PayPalCheckout>
     */
    private ReflectionClass $reflection;

    /**
     * @var bool Original sandbox flag value for restoration.
     */
    private bool $originalSandboxFlag;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(PayPalCheckout::class);
        $this->originalSandboxFlag = PayPalCheckout::$sandboxFlag;
    }

    protected function tearDown(): void
    {
        PayPalCheckout::$sandboxFlag = $this->originalSandboxFlag;
    }

    /**
     * Tests that the PayPalCheckout class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void
    {
        $checkout = new PayPalCheckout();
        $this->assertInstanceOf(PayPalCheckout::class, $checkout);
    }

    /**
     * Tests that the class is in the correct namespace.
     *
     * @return void
     */
    public function testClassIsInCorrectNamespace(): void
    {
        $this->assertSame('Detain\MyAdminPaypal', $this->reflection->getNamespaceName());
    }

    /**
     * Tests that all expected static properties exist.
     *
     * @return void
     */
    public function testHasExpectedStaticProperties(): void
    {
        $expected = [
            'sandboxFlag',
            'sBNCode',
            'sandboxApiEndpoint',
            'sandboxPaypalUrl',
            'sandboxPaypalDgUrl',
            'liveApiEndpoint',
            'livePaypalUrl',
            'livePaypalDgUrl',
            'proxyHost',
            'proxyPort',
            'useProxy',
            'version',
        ];

        foreach ($expected as $prop) {
            $this->assertTrue(
                $this->reflection->hasProperty($prop),
                "Property \${$prop} should exist"
            );
            $property = $this->reflection->getProperty($prop);
            $this->assertTrue($property->isStatic(), "Property \${$prop} should be static");
            $this->assertTrue($property->isPublic(), "Property \${$prop} should be public");
        }
    }

    /**
     * Tests the default value of sandboxFlag is false.
     *
     * @return void
     */
    public function testSandboxFlagDefaultIsFalse(): void
    {
        $property = $this->reflection->getProperty('sandboxFlag');
        $this->assertFalse($property->getDefaultValue());
    }

    /**
     * Tests the default value of useProxy is false.
     *
     * @return void
     */
    public function testUseProxyDefaultIsFalse(): void
    {
        $property = $this->reflection->getProperty('useProxy');
        $this->assertFalse($property->getDefaultValue());
    }

    /**
     * Tests that version is set to a valid string.
     *
     * @return void
     */
    public function testVersionIsNonEmptyString(): void
    {
        $this->assertIsString(PayPalCheckout::$version);
        $this->assertNotEmpty(PayPalCheckout::$version);
    }

    /**
     * Tests that the BN code is set.
     *
     * @return void
     */
    public function testBnCodeIsSet(): void
    {
        $this->assertSame('PP-ECWizard', PayPalCheckout::$sBNCode);
    }

    // --- Endpoint URL tests ---

    /**
     * Tests that sandbox API endpoint uses sandbox domain.
     *
     * @return void
     */
    public function testSandboxApiEndpointContainsSandbox(): void
    {
        $this->assertStringContainsString('sandbox', PayPalCheckout::$sandboxApiEndpoint);
    }

    /**
     * Tests that live API endpoint does not contain sandbox.
     *
     * @return void
     */
    public function testLiveApiEndpointDoesNotContainSandbox(): void
    {
        $this->assertStringNotContainsString('sandbox', PayPalCheckout::$liveApiEndpoint);
    }

    /**
     * Tests that sandbox PayPal URL contains sandbox domain.
     *
     * @return void
     */
    public function testSandboxPaypalUrlContainsSandbox(): void
    {
        $this->assertStringContainsString('sandbox', PayPalCheckout::$sandboxPaypalUrl);
    }

    /**
     * Tests that live PayPal URL does not contain sandbox.
     *
     * @return void
     */
    public function testLivePaypalUrlDoesNotContainSandbox(): void
    {
        $this->assertStringNotContainsString('sandbox', PayPalCheckout::$livePaypalUrl);
    }

    /**
     * Tests that sandbox DG URL contains sandbox domain.
     *
     * @return void
     */
    public function testSandboxPaypalDgUrlContainsSandbox(): void
    {
        $this->assertStringContainsString('sandbox', PayPalCheckout::$sandboxPaypalDgUrl);
    }

    /**
     * Tests that live DG URL does not contain sandbox.
     *
     * @return void
     */
    public function testLivePaypalDgUrlDoesNotContainSandbox(): void
    {
        $this->assertStringNotContainsString('sandbox', PayPalCheckout::$livePaypalDgUrl);
    }

    // --- getApiEndpoint tests ---

    /**
     * Tests that getApiEndpoint returns sandbox endpoint when sandbox is enabled.
     *
     * @return void
     */
    public function testGetApiEndpointReturnsSandboxWhenEnabled(): void
    {
        PayPalCheckout::$sandboxFlag = true;
        $this->assertSame(PayPalCheckout::$sandboxApiEndpoint, PayPalCheckout::getApiEndpoint());
    }

    /**
     * Tests that getApiEndpoint returns live endpoint when sandbox is disabled.
     *
     * @return void
     */
    public function testGetApiEndpointReturnsLiveWhenDisabled(): void
    {
        PayPalCheckout::$sandboxFlag = false;
        $this->assertSame(PayPalCheckout::$liveApiEndpoint, PayPalCheckout::getApiEndpoint());
    }

    // --- getApiPaypalUrl tests ---

    /**
     * Tests that getApiPaypalUrl returns sandbox URL when sandbox is enabled.
     *
     * @return void
     */
    public function testGetApiPaypalUrlReturnsSandboxWhenEnabled(): void
    {
        PayPalCheckout::$sandboxFlag = true;
        $this->assertSame(PayPalCheckout::$sandboxPaypalUrl, PayPalCheckout::getApiPaypalUrl());
    }

    /**
     * Tests that getApiPaypalUrl returns live URL when sandbox is disabled.
     *
     * @return void
     */
    public function testGetApiPaypalUrlReturnsLiveWhenDisabled(): void
    {
        PayPalCheckout::$sandboxFlag = false;
        $this->assertSame(PayPalCheckout::$livePaypalUrl, PayPalCheckout::getApiPaypalUrl());
    }

    // --- getApiPaypalDgUrl tests ---

    /**
     * Tests that getApiPaypalDgUrl returns sandbox DG URL when sandbox is enabled.
     *
     * @return void
     */
    public function testGetApiPaypalDgUrlReturnsSandboxWhenEnabled(): void
    {
        PayPalCheckout::$sandboxFlag = true;
        $this->assertSame(PayPalCheckout::$sandboxPaypalDgUrl, PayPalCheckout::getApiPaypalDgUrl());
    }

    /**
     * Tests that getApiPaypalDgUrl returns live DG URL when sandbox is disabled.
     *
     * @return void
     */
    public function testGetApiPaypalDgUrlReturnsLiveWhenDisabled(): void
    {
        PayPalCheckout::$sandboxFlag = false;
        $this->assertSame(PayPalCheckout::$livePaypalDgUrl, PayPalCheckout::getApiPaypalDgUrl());
    }

    // --- deformatNVP tests ---

    /**
     * Tests that deformatNVP parses a simple NVP string correctly.
     *
     * @return void
     */
    public function testDeformatNvpParsesSimpleString(): void
    {
        $nvpStr = 'KEY1=value1&KEY2=value2&KEY3=value3';
        $result = PayPalCheckout::deformatNVP($nvpStr);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('KEY1', $result);
        $this->assertArrayHasKey('KEY2', $result);
        $this->assertArrayHasKey('KEY3', $result);
        $this->assertSame('value1', $result['KEY1']);
        $this->assertSame('value2', $result['KEY2']);
        $this->assertSame('value3', $result['KEY3']);
    }

    /**
     * Tests that deformatNVP handles URL-encoded values.
     *
     * @return void
     */
    public function testDeformatNvpDecodesUrlEncodedValues(): void
    {
        $nvpStr = 'NAME=' . urlencode('John Doe') . '&EMAIL=' . urlencode('john@example.com');
        $result = PayPalCheckout::deformatNVP($nvpStr);

        $this->assertSame('John Doe', $result['NAME']);
        $this->assertSame('john@example.com', $result['EMAIL']);
    }

    /**
     * Tests that deformatNVP returns an empty array for an empty string.
     *
     * @return void
     */
    public function testDeformatNvpReturnsEmptyArrayForEmptyString(): void
    {
        $result = PayPalCheckout::deformatNVP('');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Tests that deformatNVP handles a single key-value pair without trailing ampersand.
     *
     * @return void
     */
    public function testDeformatNvpHandlesSinglePair(): void
    {
        $nvpStr = 'TOKEN=EC-123456789';
        $result = PayPalCheckout::deformatNVP($nvpStr);

        $this->assertCount(1, $result);
        $this->assertSame('EC-123456789', $result['TOKEN']);
    }

    /**
     * Tests that deformatNVP handles PayPal-like response strings.
     *
     * @return void
     */
    public function testDeformatNvpHandlesPaypalLikeResponse(): void
    {
        $nvpStr = 'ACK=Success&TOKEN=EC-12345&VERSION=109.0&BUILD=12345678';
        $result = PayPalCheckout::deformatNVP($nvpStr);

        $this->assertSame('Success', $result['ACK']);
        $this->assertSame('EC-12345', $result['TOKEN']);
        $this->assertSame('109.0', $result['VERSION']);
        $this->assertSame('12345678', $result['BUILD']);
    }

    /**
     * Tests that deformatNVP handles URL-encoded keys.
     *
     * @return void
     */
    public function testDeformatNvpDecodesUrlEncodedKeys(): void
    {
        $nvpStr = urlencode('PAYMENT AMT') . '=100.00';
        $result = PayPalCheckout::deformatNVP($nvpStr);

        $this->assertArrayHasKey('PAYMENT AMT', $result);
        $this->assertSame('100.00', $result['PAYMENT AMT']);
    }

    // --- Method existence and signature tests ---

    /**
     * Tests that all expected public static methods exist.
     *
     * @return void
     */
    public function testHasExpectedPublicStaticMethods(): void
    {
        $expectedMethods = [
            'setSessionData',
            'getApiPaypalDgUrl',
            'getApiPaypalUrl',
            'getApiEndpoint',
            'getApiUsername',
            'getApiPassword',
            'getApiSignature',
            'SetSubscriptionExpressCheckout',
            'CreateRecurringPaymentsProfile',
            'SetExpressCheckoutDG',
            'SetExpressCheckout',
            'CallShortcutExpressCheckout',
            'CallMarkExpressCheckout',
            'GetExpressCheckoutDetails',
            'ConfirmPayment',
            'DirectPayment',
            'paypal_hash_call',
            'RedirectToPayPal',
            'RedirectToPayPalDG',
            'deformatNVP',
        ];

        foreach ($expectedMethods as $methodName) {
            $this->assertTrue(
                $this->reflection->hasMethod($methodName),
                "Method '{$methodName}' should exist"
            );

            $method = $this->reflection->getMethod($methodName);
            $this->assertTrue($method->isStatic(), "Method '{$methodName}' should be static");
            $this->assertTrue($method->isPublic(), "Method '{$methodName}' should be public");
        }
    }

    /**
     * Tests that SetExpressCheckout method has the expected parameter count.
     *
     * @return void
     */
    public function testSetExpressCheckoutParameterCount(): void
    {
        $method = $this->reflection->getMethod('SetExpressCheckout');
        $params = $method->getParameters();

        // paymentAmount, currencyCodeType, paymentType, returnURL, cancelURL, items, custom
        $this->assertCount(7, $params);
        $this->assertSame('paymentAmount', $params[0]->getName());
        $this->assertSame('items', $params[5]->getName());
        $this->assertTrue($params[6]->isOptional(), 'custom param should be optional');
    }

    /**
     * Tests that SetExpressCheckoutDG method has the expected parameter count.
     *
     * @return void
     */
    public function testSetExpressCheckoutDgParameterCount(): void
    {
        $method = $this->reflection->getMethod('SetExpressCheckoutDG');
        $params = $method->getParameters();

        // paymentAmount, currencyCodeType, paymentType, returnURL, cancelURL, items
        $this->assertCount(6, $params);
    }

    /**
     * Tests that SetSubscriptionExpressCheckout has correct parameters.
     *
     * @return void
     */
    public function testSetSubscriptionExpressCheckoutParameters(): void
    {
        $method = $this->reflection->getMethod('SetSubscriptionExpressCheckout');
        $params = $method->getParameters();

        $this->assertCount(10, $params);

        $requiredNames = ['paymentAmount', 'currencyCodeType', 'paymentType', 'returnURL', 'cancelURL', 'items'];
        foreach ($requiredNames as $index => $name) {
            $this->assertSame($name, $params[$index]->getName());
            $this->assertFalse($params[$index]->isOptional(), "Param '{$name}' should be required");
        }

        // Optional params
        $this->assertTrue($params[6]->isOptional(), 'period should be optional');
        $this->assertTrue($params[7]->isOptional(), 'repeat_amount should be optional');
        $this->assertTrue($params[8]->isOptional(), 'category should be optional');
        $this->assertTrue($params[9]->isOptional(), 'custom should be optional');
    }

    /**
     * Tests that CreateRecurringPaymentsProfile has correct parameters.
     *
     * @return void
     */
    public function testCreateRecurringPaymentsProfileParameters(): void
    {
        $method = $this->reflection->getMethod('CreateRecurringPaymentsProfile');
        $params = $method->getParameters();

        $this->assertCount(6, $params);
        $this->assertSame('token', $params[0]->getName());
        $this->assertSame('payer_id', $params[1]->getName());
        $this->assertSame('amt', $params[2]->getName());
        $this->assertSame('period', $params[3]->getName());
        $this->assertSame('description', $params[4]->getName());
        $this->assertTrue($params[5]->isOptional(), 'initamt should be optional');
    }

    /**
     * Tests that ConfirmPayment has the correct parameter count.
     *
     * @return void
     */
    public function testConfirmPaymentParameters(): void
    {
        $method = $this->reflection->getMethod('ConfirmPayment');
        $params = $method->getParameters();

        $this->assertCount(5, $params);
        $this->assertSame('token', $params[0]->getName());
        $this->assertSame('paymentType', $params[1]->getName());
        $this->assertSame('FinalPaymentAmt', $params[4]->getName());
    }

    /**
     * Tests that DirectPayment has the correct number of parameters (all required).
     *
     * @return void
     */
    public function testDirectPaymentParameters(): void
    {
        $method = $this->reflection->getMethod('DirectPayment');
        $params = $method->getParameters();

        $this->assertCount(14, $params);

        foreach ($params as $param) {
            $this->assertFalse($param->isOptional(), "Param '{$param->getName()}' should be required");
        }
    }

    /**
     * Tests that CallMarkExpressCheckout has the correct number of parameters.
     *
     * @return void
     */
    public function testCallMarkExpressCheckoutParameters(): void
    {
        $method = $this->reflection->getMethod('CallMarkExpressCheckout');
        $params = $method->getParameters();

        $this->assertCount(13, $params);
        $this->assertSame('shipToName', $params[5]->getName());
        $this->assertSame('phoneNum', $params[12]->getName());
    }

    /**
     * Tests that paypal_hash_call accepts method name and NVP string.
     *
     * @return void
     */
    public function testPaypalHashCallParameters(): void
    {
        $method = $this->reflection->getMethod('paypal_hash_call');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('methodName', $params[0]->getName());
        $this->assertSame('nvpStr', $params[1]->getName());
    }

    /**
     * Tests that the constructor takes no parameters.
     *
     * @return void
     */
    public function testConstructorTakesNoParameters(): void
    {
        $constructor = $this->reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(0, $constructor->getParameters());
    }

    /**
     * Tests that all static endpoint URLs are valid HTTPS URLs.
     *
     * @return void
     */
    public function testAllEndpointUrlsAreValidHttps(): void
    {
        $urlProperties = [
            'sandboxApiEndpoint',
            'sandboxPaypalUrl',
            'sandboxPaypalDgUrl',
            'liveApiEndpoint',
            'livePaypalUrl',
            'livePaypalDgUrl',
        ];

        foreach ($urlProperties as $prop) {
            $value = $this->reflection->getProperty($prop)->getDefaultValue();
            $this->assertStringStartsWith('https://', $value, "Property \${$prop} should be an HTTPS URL");
        }
    }

    /**
     * Tests that all sandbox URLs point to sandbox.paypal.com.
     *
     * @return void
     */
    public function testSandboxUrlsPointToSandboxDomain(): void
    {
        $this->assertStringContainsString('sandbox.paypal.com', PayPalCheckout::$sandboxApiEndpoint);
        $this->assertStringContainsString('sandbox.paypal.com', PayPalCheckout::$sandboxPaypalUrl);
        $this->assertStringContainsString('sandbox.paypal.com', PayPalCheckout::$sandboxPaypalDgUrl);
    }

    /**
     * Tests that all live URLs point to paypal.com (not sandbox).
     *
     * @return void
     */
    public function testLiveUrlsPointToPaypalDomain(): void
    {
        $this->assertStringContainsString('paypal.com', PayPalCheckout::$liveApiEndpoint);
        $this->assertStringContainsString('paypal.com', PayPalCheckout::$livePaypalUrl);
        $this->assertStringContainsString('paypal.com', PayPalCheckout::$livePaypalDgUrl);
    }
}
