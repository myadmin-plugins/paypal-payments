---
name: paypal-plugin-pattern
description: Adds new event hooks, settings, or function requirements to src/Plugin.php following the existing getHooks()/getSettings()/getRequirements() pattern with Symfony GenericEvent. Use when user says 'add a setting', 'register a hook', 'add new requirement', or modifies src/Plugin.php. Do NOT use for admin page logic, test writing, IPN handling, or PayPalCheckout class changes.
---
# PayPal Plugin Pattern

Guide for adding event hooks, settings, and function/page requirements to `src/Plugin.php`.

## Critical

- **All handler methods MUST be `public static` and accept a single `Symfony\Component\EventDispatcher\GenericEvent $event` parameter.** No exceptions.
- **Every hook registered in `getHooks()` MUST have a corresponding static method on the `Plugin` class.** The format is `'event.name' => [__CLASS__, 'methodName']`.
- **Every new setting that references a constant MUST have that constant defined in `tests/bootstrap.php`** with a test stub value, wrapped in `if (!defined('CONSTANT_NAME'))`. Otherwise tests break.
- **Requirement paths MUST follow the format used in `src/Plugin.php` `getRequirements()` method** — this is how the MyAdmin plugin loader resolves paths at runtime.
- **All user-facing label strings MUST be wrapped in `_()`** for gettext i18n.

## Instructions

### Adding a New Setting

1. Open `src/Plugin.php` and locate the `getSettings()` method.
2. Identify the correct setting type from the existing patterns:
   - `add_radio_setting($category, $group, $key, $label, $description, $default, $options, $optionLabels)` — for boolean/enum toggles
   - `add_text_setting($category, $group, $key, $label, $description, $default)` — for plaintext fields
   - `add_password_setting($category, $group, $key, $label, $description, $default)` — for secrets/credentials
3. Add the setting call inside `getSettings()`. Follow these conventions exactly:
   - Category is `_('Billing')`, group is `_('PayPal')` — matches all existing settings
   - Key is `snake_case`, prefixed with `paypal_` (e.g., `'paypal_webhook_url'`)
   - Default value uses the guarded constant pattern: `(defined('PAYPAL_WEBHOOK_URL') ? PAYPAL_WEBHOOK_URL : '')`
   - For radio settings, the current value comes from the constant directly (e.g., `PAYPAL_ENABLE`), options are `[true, false]`, labels are `['Enabled', 'Disabled']`
4. **Verify:** The constant name matches the uppercased key (e.g., key `paypal_webhook_url` → constant `PAYPAL_WEBHOOK_URL`).
5. Open `tests/bootstrap.php` and add the constant stub:
   ```php
   if (!defined('PAYPAL_WEBHOOK_URL')) {
       define('PAYPAL_WEBHOOK_URL', 'test_webhook_url');
   }
   ```
6. **Verify:** Run `vendor/bin/phpunit tests/PluginTest.php` — all tests must pass.

### Adding a New Hook

1. Open `src/Plugin.php` and add the event mapping to the `getHooks()` return array:
   ```php
   public static function getHooks()
   {
       return [
           'system.settings' => [__CLASS__, 'getSettings'],
           'function.requirements' => [__CLASS__, 'getRequirements'],
           'your.event.name' => [__CLASS__, 'yourHandlerMethod'],
       ];
   }
   ```
2. Add the corresponding handler method on the `Plugin` class. It MUST be:
   - `public static`
   - Accept exactly one parameter: `GenericEvent $event`
   - Use `$event->getSubject()` to get the subject object passed by the dispatcher
3. The subject type depends on the event:
   - `system.settings` → `\MyAdmin\Settings` instance
   - `function.requirements` → `\MyAdmin\Plugins\Loader` instance
   - `ui.menu` → menu array (see the commented-out `getMenu()` for the pattern)
4. **Verify:** Every string key in the `getHooks()` array has a matching callable method. Run `vendor/bin/phpunit tests/PluginTest.php` — the `testAllHookHandlerMethodsExist` test checks this automatically.

### Adding a New Function Requirement

1. Open `src/Plugin.php` and locate the `getRequirements()` method.
2. Choose the correct loader method:
   - `$loader->add_page_requirement($name, $path)` — for admin page entry points (files in `src/admin/`). The `$name` is the page function name (e.g., `'paypal_refund'`).
   - `$loader->add_requirement($name, $path)` — for utility/library functions (files in `src/`). The `$name` is the function name (e.g., `'get_paypal_link_url'`).
3. The path format follows the existing entries in `src/Plugin.php` `getRequirements()`. See lines 60-63 for page paths and lines 64-81 for function paths. You can inspect the existing format with:
   ```bash
   grep 'add_page_requirement\|add_requirement' src/Plugin.php
   ```
4. Multiple functions from the same file share the same path — see how all `get_paypal_*_vars` functions point to `src/paypal.functions.inc.php`.
5. **Verify:** The target file exists at the specified path relative to the plugin root. Run `vendor/bin/phpunit tests/PluginTest.php`.

### Enabling the Commented-Out `ui.menu` Hook

1. In `getHooks()`, uncomment the line:
   ```php
   'ui.menu' => [__CLASS__, 'getMenu'],
   ```
2. The `getMenu()` method already exists on the class. It checks `$GLOBALS['tf']->ima == 'admin'` and uses `function_requirements('has_acl')` + `has_acl('client_billing')` for access control.
3. Add menu items inside the ACL check block.
4. **Verify:** The `testGetHooksReturnsExpectedKeys` test will now also expect `'ui.menu'` — update the test if needed.

## Examples

### Example: Add a PayPal Sandbox Toggle Setting

**User says:** "Add a setting to enable/disable PayPal sandbox mode"

**Actions:**

1. Edit `src/Plugin.php` — add to `getSettings()`:
   ```php
   $settings->add_radio_setting(_('Billing'), _('PayPal'), 'paypal_sandbox_enable', _('Enable Sandbox'), _('Enable Sandbox Mode'), PAYPAL_SANDBOX_ENABLE, [true, false], ['Enabled', 'Disabled']);
   ```

2. Edit `tests/bootstrap.php` — add constant:
   ```php
   if (!defined('PAYPAL_SANDBOX_ENABLE')) {
       define('PAYPAL_SANDBOX_ENABLE', false);
   }
   ```

3. Run `vendor/bin/phpunit tests/PluginTest.php` — passes.

**Result:** New "Enable Sandbox" radio toggle appears under Billing → PayPal in admin settings.

### Example: Register a New Admin Page Requirement

**User says:** "Add a page for PayPal dispute management at `src/admin/paypal_disputes.php`"

**Actions:**

1. Create `src/admin/paypal_disputes.php` with the admin page logic.
2. Edit `src/Plugin.php` — add to `getRequirements()` following the path format from the existing entries (see `src/Plugin.php` lines 60-63):
   ```php
   $loader->add_page_requirement('paypal_disputes', $pathPrefix . 'src/admin/paypal_disputes.php');
   ```

3. Run `vendor/bin/phpunit` — passes.

**Result:** The `paypal_disputes` page is loadable via `function_requirements('paypal_disputes')` in the MyAdmin system.

### Example: Add a New Event Hook

**User says:** "Register a hook for the `billing.payment.success` event"

**Actions:**

1. Edit `getHooks()` in `src/Plugin.php`:
   ```php
   return [
       'system.settings' => [__CLASS__, 'getSettings'],
       'function.requirements' => [__CLASS__, 'getRequirements'],
       'billing.payment.success' => [__CLASS__, 'onPaymentSuccess'],
   ];
   ```

2. Add the handler method:
   ```php
   /**
    * @param \Symfony\Component\EventDispatcher\GenericEvent $event
    */
   public static function onPaymentSuccess(GenericEvent $event)
   {
       $paymentData = $event->getSubject();
       // Handle post-payment logic
   }
   ```

3. Run `vendor/bin/phpunit tests/PluginTest.php` — `testAllHookHandlerMethodsExist` validates the new method exists.

## Common Issues

### `PHP Fatal error: Undefined constant 'PAYPAL_NEW_SETTING'`
You added a setting referencing a constant but forgot to define it in `tests/bootstrap.php`. Fix: add the `if (!defined(...))` block to `tests/bootstrap.php`.

### `testAllHookHandlerMethodsExist` fails after adding a hook
The method name in `getHooks()` does not match any method on `Plugin`. Fix: ensure the second element of the handler array (e.g., `'onPaymentSuccess'`) exactly matches the method name, including case.

### `testGetHooksReturnsExpectedKeys` fails after adding a hook
The test asserts specific keys. If you add a new hook, the existing test still passes (it only checks that `system.settings` and `function.requirements` exist). But if you remove or rename an existing hook, the test will fail. Fix: update the test assertions to match.

### Requirement path not resolving at runtime
The path must follow the format used in the existing `getRequirements()` entries in `src/Plugin.php`. This relative path is resolved from the MyAdmin `include/` directory at runtime. Double-check there are no typos and the target file exists.

### Setting not appearing in admin panel
Ensure the category and group strings match existing ones exactly: `_('Billing')` and `_('PayPal')`. A typo like `_('Paypal')` (lowercase p) creates a separate group.

### `add_page_requirement` vs `add_requirement` confusion
- `add_page_requirement`: for files that define an admin page entry point (routable via URL). Used for files in `src/admin/`.
- `add_requirement`: for files that define utility functions callable via `function_requirements()`. Used for library files in `src/`.

Using the wrong one won't error, but the function/page won't be discoverable through the expected mechanism.
