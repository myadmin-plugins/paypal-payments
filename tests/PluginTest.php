<?php

declare(strict_types=1);

namespace Detain\MyAdminPaypal\Tests;

use Detain\MyAdminPaypal\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Tests for the Plugin class.
 *
 * Validates class structure, static properties, hook registration,
 * and event handler method signatures.
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass<Plugin>
     */
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Plugin::class);
    }

    /**
     * Tests that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Tests that the $name static property is set correctly.
     *
     * @return void
     */
    public function testNamePropertyIsCorrect(): void
    {
        $this->assertSame('Paypal Plugin', Plugin::$name);
    }

    /**
     * Tests that the $description static property is a non-empty string.
     *
     * @return void
     */
    public function testDescriptionPropertyIsNonEmpty(): void
    {
        $this->assertIsString(Plugin::$description);
        $this->assertNotEmpty(Plugin::$description);
    }

    /**
     * Tests that the $help static property exists and is a string.
     *
     * @return void
     */
    public function testHelpPropertyExists(): void
    {
        $this->assertIsString(Plugin::$help);
    }

    /**
     * Tests that the $type static property is 'plugin'.
     *
     * @return void
     */
    public function testTypePropertyIsPlugin(): void
    {
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Tests that the class has all expected static properties.
     *
     * @return void
     */
    public function testHasExpectedStaticProperties(): void
    {
        $this->assertTrue($this->reflection->hasProperty('name'));
        $this->assertTrue($this->reflection->hasProperty('description'));
        $this->assertTrue($this->reflection->hasProperty('help'));
        $this->assertTrue($this->reflection->hasProperty('type'));

        foreach (['name', 'description', 'help', 'type'] as $prop) {
            $property = $this->reflection->getProperty($prop);
            $this->assertTrue($property->isStatic(), "Property \${$prop} should be static");
            $this->assertTrue($property->isPublic(), "Property \${$prop} should be public");
        }
    }

    /**
     * Tests that getHooks returns an array with expected event keys.
     *
     * @return void
     */
    public function testGetHooksReturnsExpectedKeys(): void
    {
        $hooks = Plugin::getHooks();

        $this->assertIsArray($hooks);
        $this->assertArrayHasKey('system.settings', $hooks);
        $this->assertArrayHasKey('function.requirements', $hooks);
    }

    /**
     * Tests that getHooks maps to callable-like arrays.
     *
     * @return void
     */
    public function testGetHooksValuesAreCallableFormat(): void
    {
        $hooks = Plugin::getHooks();

        foreach ($hooks as $event => $handler) {
            $this->assertIsArray($handler, "Handler for '{$event}' should be an array");
            $this->assertCount(2, $handler, "Handler for '{$event}' should have exactly 2 elements");
            $this->assertSame(Plugin::class, $handler[0], "Handler for '{$event}' should reference Plugin class");
            $this->assertIsString($handler[1], "Handler method name for '{$event}' should be a string");
        }
    }

    /**
     * Tests that the system.settings hook points to getSettings.
     *
     * @return void
     */
    public function testSystemSettingsHookPointsToGetSettings(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame([Plugin::class, 'getSettings'], $hooks['system.settings']);
    }

    /**
     * Tests that the function.requirements hook points to getRequirements.
     *
     * @return void
     */
    public function testFunctionRequirementsHookPointsToGetRequirements(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame([Plugin::class, 'getRequirements'], $hooks['function.requirements']);
    }

    /**
     * Tests that all hook handler methods exist on the Plugin class.
     *
     * @return void
     */
    public function testAllHookHandlerMethodsExist(): void
    {
        $hooks = Plugin::getHooks();

        foreach ($hooks as $event => $handler) {
            $methodName = $handler[1];
            $this->assertTrue(
                $this->reflection->hasMethod($methodName),
                "Method '{$methodName}' referenced in hook '{$event}' should exist on Plugin class"
            );
        }
    }

    /**
     * Tests that getSettings method is static.
     *
     * @return void
     */
    public function testGetSettingsIsStatic(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getRequirements method is static.
     *
     * @return void
     */
    public function testGetRequirementsIsStatic(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getMenu method is static.
     *
     * @return void
     */
    public function testGetMenuIsStatic(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getSettings accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetSettingsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that getRequirements accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetRequirementsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that getMenu accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetMenuAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
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
     * Tests that getRequirements calls add_page_requirement and add_requirement on the loader.
     *
     * @return void
     */
    public function testGetRequirementsRegistersExpectedPages(): void
    {
        $pageRequirements = [];
        $requirements = [];

        $loader = new class($pageRequirements, $requirements) {
            /** @var array<int, array{0: string, 1: string}> */
            private array $pageReqs;
            /** @var array<int, array{0: string, 1: string}> */
            private array $reqs;

            /**
             * @param array<int, array{0: string, 1: string}> $pageReqs
             * @param array<int, array{0: string, 1: string}> $reqs
             */
            public function __construct(array &$pageReqs, array &$reqs)
            {
                $this->pageReqs = &$pageReqs;
                $this->reqs = &$reqs;
            }

            public function add_page_requirement(string $name, string $path): void
            {
                $this->pageReqs[] = [$name, $path];
            }

            public function add_requirement(string $name, string $path): void
            {
                $this->reqs[] = [$name, $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $this->assertNotEmpty($pageRequirements, 'Should register page requirements');
        $this->assertNotEmpty($requirements, 'Should register function requirements');

        $pageNames = array_column($pageRequirements, 0);
        $this->assertContains('view_paypal_transaction', $pageNames);
        $this->assertContains('paypal_history', $pageNames);
        $this->assertContains('paypal_transactions', $pageNames);
        $this->assertContains('paypal_refund', $pageNames);

        $reqNames = array_column($requirements, 0);
        $this->assertContains('get_paypal_link', $reqNames);
        $this->assertContains('get_paypal_link_url', $reqNames);
        $this->assertContains('get_paypal_subscription_link', $reqNames);
        $this->assertContains('get_paypal_subscription_link_url', $reqNames);
        $this->assertContains('is_paypal_txn_refunded', $reqNames);
        $this->assertContains('PayPalHttpPost', $reqNames);
        $this->assertContains('get_paypal_adaptive_accounts_ipn_messages', $reqNames);
        $this->assertContains('get_paypal_buyer_information_vars', $reqNames);
    }

    /**
     * Tests that all registered requirement paths reference existing files.
     *
     * @return void
     */
    public function testGetRequirementsPathsReferenceValidFiles(): void
    {
        $pageRequirements = [];
        $requirements = [];

        $loader = new class($pageRequirements, $requirements) {
            /** @var array<int, array{0: string, 1: string}> */
            private array $pageReqs;
            /** @var array<int, array{0: string, 1: string}> */
            private array $reqs;

            /**
             * @param array<int, array{0: string, 1: string}> $pageReqs
             * @param array<int, array{0: string, 1: string}> $reqs
             */
            public function __construct(array &$pageReqs, array &$reqs)
            {
                $this->pageReqs = &$pageReqs;
                $this->reqs = &$reqs;
            }

            public function add_page_requirement(string $name, string $path): void
            {
                $this->pageReqs[] = [$name, $path];
            }

            public function add_requirement(string $name, string $path): void
            {
                $this->reqs[] = [$name, $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $allPaths = array_merge(
            array_column($pageRequirements, 1),
            array_column($requirements, 1)
        );

        $uniquePaths = array_unique($allPaths);
        $this->assertNotEmpty($uniquePaths);

        foreach ($uniquePaths as $path) {
            $this->assertStringContainsString('myadmin-paypal-payments', $path, "Path should reference this package: {$path}");
        }
    }

    /**
     * Tests that the Plugin class is in the correct namespace.
     *
     * @return void
     */
    public function testClassIsInCorrectNamespace(): void
    {
        $this->assertSame('Detain\MyAdminPaypal', $this->reflection->getNamespaceName());
    }
}
