---
name: phpunit-tests
description: Writes PHPUnit 9 tests in tests/ following existing patterns: bootstrap constant stubs from tests/bootstrap.php, ReflectionClass/ReflectionFunction for signature checks, function_exists assertions, data providers. Use when user says 'add tests', 'write test', 'test coverage', or creates new src/ files. Do NOT use for integration tests requiring database.
---
# PHPUnit Tests

## Critical

- **PHPUnit 9 only** — do not use PHPUnit 10+ attributes. Use `@dataProvider` and `@return void` docblock annotations.
- **Never call external APIs or databases** — these are unit tests. Validate structure, signatures, return types, and existence only.
- **Always use `declare(strict_types=1);`** at the top of every test file.
- **Namespace**: `Detain\MyAdminPaypal\Tests` — all test classes go in `tests/`.
- **Bootstrap**: `tests/bootstrap.php` defines stub constants (`PAYPAL_ENABLE`, `PAYPAL_EMAIL`, `PAYPAL_API_USERNAME`, etc.) and creates a config stub file. If the code under test requires a constant, add it to bootstrap with `if (!defined('X')) { define('X', 'test_value'); }` — never redefine.
- **Config**: `phpunit.xml.dist` at project root, bootstrap `tests/bootstrap.php`, testsuite scans `tests/` directory.

## Instructions

### 1. Create the test file

File naming follows the existing convention, e.g., `tests/PayPalCheckoutTest.php` tests `src/PayPalCheckout.php`, `tests/PaypalFunctionsTest.php` tests `src/paypal.functions.inc.php`.

```php
<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;
// Add use statements for classes under test and Reflection classes as needed

class PayPalCheckoutTest extends TestCase
{
}
```

Verify: File is in `tests/`, namespace is `Detain\MyAdminPaypal\Tests`, extends `TestCase`.

### 2. Choose the right test pattern based on what you're testing

**Pattern A: Testing a PSR-4 class (e.g., `src/Plugin.php`, `src/PayPalCheckout.php`)**

Use `ReflectionClass` in `setUp()` to inspect structure without side effects:

```php
use Detain\MyAdminPaypal\PayPalCheckout;
use ReflectionClass;

private ReflectionClass $reflection;

protected function setUp(): void
{
    $this->reflection = new ReflectionClass(PayPalCheckout::class);
}
```

Test categories for classes:
- Instantiation: `$this->assertInstanceOf(PayPalCheckout::class, new PayPalCheckout());`
- Namespace: `$this->assertSame('Detain\MyAdminPaypal', $this->reflection->getNamespaceName());`
- Static properties exist and are public/static:
  ```php
  $this->assertTrue($this->reflection->hasProperty('propName'));
  $property = $this->reflection->getProperty('propName');
  $this->assertTrue($property->isStatic());
  $this->assertTrue($property->isPublic());
  ```
- Default values: `$property->getDefaultValue()`
- Method existence, visibility, and static check:
  ```php
  $method = $this->reflection->getMethod('methodName');
  $this->assertTrue($method->isStatic());
  $this->assertTrue($method->isPublic());
  ```
- Parameter signatures:
  ```php
  $params = $method->getParameters();
  $this->assertCount(3, $params);
  $this->assertSame('paramName', $params[0]->getName());
  $this->assertTrue($params[2]->isOptional());
  ```
- Parameter type hints:
  ```php
  $type = $params[0]->getType();
  $this->assertNotNull($type);
  $this->assertSame(GenericEvent::class, $type->getName());
  ```

Verify: Each test method name starts with `test`, has `@return void` docblock, return type `: void`.

**Pattern B: Testing procedural function files (e.g., `src/paypal.functions.inc.php`)**

Load the file in `setUpBeforeClass()` with a guard:

```php
public static function setUpBeforeClass(): void
{
    $file = dirname(__DIR__) . '/src/paypal.functions.inc.php';
    if (!function_exists('get_paypal_link_url')) {
        require_once $file;
    }
}
```

Test categories for procedural functions:
- Function existence: `$this->assertTrue(function_exists('get_paypal_link_url'));`
- Signature via `ReflectionFunction`:
  ```php
  $ref = new \ReflectionFunction('get_paypal_link_url');
  $params = $ref->getParameters();
  $this->assertCount(2, $params);
  $this->assertSame('param_name', $params[0]->getName());
  $this->assertTrue($params[1]->isOptional());
  $this->assertSame('default_val', $params[1]->getDefaultValue());
  ```
- Return value structure (for functions that return arrays from JSON files):
  ```php
  $result = get_paypal_buyer_information_vars();
  $this->assertIsArray($result);
  $this->assertNotEmpty($result);
  $this->assertArrayHasKey('expected_key', $result);
  ```
- String key/value validation:
  ```php
  foreach ($result as $key => $value) {
      $this->assertIsString($key);
      $this->assertIsString($value);
  }
  ```

Verify: `setUpBeforeClass` uses `dirname(__DIR__)` for path, checks `function_exists` before `require_once`.

**Pattern C: Testing file existence and validity**

Use `$this->basePath = dirname(__DIR__);` in `setUp()`, then:

```php
$this->assertFileExists($this->basePath . '/src/PayPalCheckout.php');
$this->assertFileIsReadable($this->basePath . '/src/PayPalCheckout.php');
$content = file_get_contents($fullPath);
$this->assertStringStartsWith('<?php', ltrim($content));
```

For JSON files:
```php
$decoded = json_decode($content, true);
$this->assertNotNull($decoded, 'Should contain valid JSON: ' . json_last_error_msg());
```

### 3. Use data providers for repetitive assertions

When testing multiple similar items (files, functions, keys), use a `@dataProvider`:

```php
/**
 * @dataProvider itemProvider
 * @param string $item
 * @return void
 */
public function testItemIsValid(string $item): void
{
    // assertion
}

/**
 * @return array<string, array{0: string}>
 */
public function itemProvider(): array
{
    return [
        'label_one' => ['value_one'],
        'label_two' => ['value_two'],
    ];
}
```

Verify: Provider method is `public`, returns `array<string, array{...}>`, keys are descriptive labels.

### 4. Handle mutable static state with setUp/tearDown

If tests modify static properties, save and restore in `setUp()`/`tearDown()`:

```php
private bool $originalValue;

protected function setUp(): void
{
    $this->originalValue = PayPalCheckout::$sandboxFlag;
}

protected function tearDown(): void
{
    PayPalCheckout::$sandboxFlag = $this->originalValue;
}
```

Verify: Any test that sets a static property has corresponding tearDown restoration.

### 5. Add constants to bootstrap if needed

If the new source file references constants not yet in `tests/bootstrap.php`, add them:

```php
if (!defined('NEW_CONSTANT')) {
    define('NEW_CONSTANT', 'test_value');
}
```

Verify: Constant is wrapped in `if (!defined(...))` guard. Use safe test values, never real credentials.

### 6. Run the tests

```bash
vendor/bin/phpunit                          # all tests
vendor/bin/phpunit tests/PayPalCheckoutTest.php   # single file
vendor/bin/phpunit --coverage-text          # with coverage
```

Verify: All tests pass. No warnings about risky tests (strict mode is on in `phpunit.xml.dist` — `failOnRisky="true"`, `beStrictAboutOutputDuringTests="true"`).

## Examples

### Example: Adding tests for `src/PayPalCheckout.php`

User says: "Add tests for the PayPalCheckout class"

Actions:
1. Read `src/PayPalCheckout.php` to understand its structure
2. Create `tests/PayPalCheckoutTest.php`
3. If it uses new constants, add them to `tests/bootstrap.php`

Result (`tests/PayPalCheckoutTest.php`):
```php
<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use Detain\MyAdminPaypal\PayPalCheckout;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PayPalCheckoutTest extends TestCase
{
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(PayPalCheckout::class);
    }

    public function testCanBeInstantiated(): void
    {
        $checkout = new PayPalCheckout();
        $this->assertInstanceOf(PayPalCheckout::class, $checkout);
    }

    public function testClassIsInCorrectNamespace(): void
    {
        $this->assertSame('Detain\MyAdminPaypal', $this->reflection->getNamespaceName());
    }

    public function testHasExpectedPublicMethods(): void
    {
        $expectedMethods = ['SetExpressCheckout', 'GetExpressCheckoutDetails', 'DoExpressCheckoutPayment'];
        foreach ($expectedMethods as $methodName) {
            $this->assertTrue(
                $this->reflection->hasMethod($methodName),
                "Method '{$methodName}' should exist"
            );
            $method = $this->reflection->getMethod($methodName);
            $this->assertTrue($method->isPublic(), "Method '{$methodName}' should be public");
        }
    }

    public function testSandboxFlagProperty(): void
    {
        $this->assertTrue($this->reflection->hasProperty('sandboxFlag'));
        $property = $this->reflection->getProperty('sandboxFlag');
        $this->assertTrue($property->isPublic());
    }
}
```

### Example: Adding tests for a procedural function file

User says: "Write tests for `src/paypal_refund.functions.php`"

Result (`tests/PaypalRefundFunctionsTest.php`):
```php
<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class PaypalRefundFunctionsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $file = dirname(__DIR__) . '/src/paypal_refund.functions.php';
        if (!function_exists('PayPalHttpPost')) {
            require_once $file;
        }
    }

    public function testPayPalHttpPostFunctionExists(): void
    {
        $this->assertTrue(function_exists('PayPalHttpPost'));
    }

    public function testPayPalHttpPostParameterSignature(): void
    {
        $ref = new ReflectionFunction('PayPalHttpPost');
        $params = $ref->getParameters();
        $this->assertCount(3, $params);
        $this->assertSame('methodName_', $params[0]->getName());
        $this->assertSame('nvpStr_', $params[1]->getName());
        $this->assertSame('sandbox', $params[2]->getName());
    }
}
```

## Common Issues

**Error: `Call to undefined function some_function()`**
1. The procedural file was not loaded. Add `setUpBeforeClass()` with `require_once dirname(__DIR__) . '/src/paypal.functions.inc.php'`
2. Guard with `if (!function_exists('some_function'))` to avoid double-include errors

**Error: `Constant PAYPAL_X already defined`**
1. The constant is being defined both in `tests/bootstrap.php` and in the test's `setUpBeforeClass`. Always wrap in `if (!defined('PAYPAL_X'))` guard
2. Check `tests/bootstrap.php` — it may already define what you need

**Error: `Trying to access array offset on value of type null` in source code**
1. Source file tries to include `config.settings.php` which doesn't exist in test env. The bootstrap creates this stub at `src/../../../../include/config/config.settings.php`. If the path changed, update bootstrap.

**Error: `This test did not perform any assertions` (risky test)**
1. `phpunit.xml.dist` has `failOnRisky="true"`. Every test method MUST contain at least one assertion.

**Error: `Test code or tested code did not (only) close its own output buffers`**
1. `beStrictAboutOutputDuringTests="true"` is set. Don't test functions that echo/print directly — test their return values or use `$this->expectOutputString()` if output is the expected behavior.

**Test passes locally but fails in CI**
1. Check if the function file requires globals like `$GLOBALS['tf']`. Admin pages check `$GLOBALS['tf']->ima == 'admin'`. For these files, only test function existence and signatures, not invocation.
2. Ensure any new constants are added to `tests/bootstrap.php`, not defined inline in test files (bootstrap runs once for all tests).

**Data provider method not found**
1. Data provider methods must be `public` (not `protected` or `private`)
2. The `@dataProvider` annotation must reference the method name without `()` — e.g., `@dataProvider myProvider` not `@dataProvider myProvider()`
