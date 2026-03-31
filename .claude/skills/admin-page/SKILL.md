---
name: admin-page
description: Creates a new admin page in src/admin/ following the ACL-check pattern (has_acl('client_billing'), $GLOBALS['tf']->ima check) and registers it in src/Plugin.php via add_page_requirement(). Use when user says 'add admin page', 'new admin view', or 'create management page'. Do NOT use for API endpoints or non-admin features.
---
# Admin Page

Creates new admin pages in `src/admin/` for the MyAdmin PayPal Payments plugin, following the established ACL-check pattern and registering them in the plugin loader.

## Critical

- Every admin page function MUST check both `$GLOBALS['tf']->ima == 'admin'` AND `has_acl('client_billing')` before executing any logic. No exceptions.
- Always call `function_requirements('has_acl')` before using `has_acl()` — it lazy-loads the function.
- The function name MUST match the filename without the extension. E.g., `src/admin/paypal_history.php` defines `function paypal_history()`.
- Never use PDO. Use `$db = clone $GLOBALS['tf']->db;` for database access.
- Always escape user input with `$db->real_escape()`. Use `make_insert_query()` for inserts.
- The page MUST be registered in `src/Plugin.php` → `getRequirements()` via `$loader->add_page_requirement()`.

## Instructions

### Step 1: Create the admin page file

Create a new file in `src/admin/` named after the function. Use this exact boilerplate (following the pattern from `src/admin/paypal_history.php`):

```php
<?php

    /**
     * Administrative Functionality
     * @author Joe Huss <detain@interserver.net>
     * @copyright 2025
     * @package MyAdmin
     * @category Admin
     */

    /**
     * paypal_disputes()
     *
     */
    function paypal_disputes()
    {
        page_title('PayPal Disputes');
        function_requirements('has_acl');
        if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
            dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
            return false;
        }
        // Page logic here
    }
```

**Verify**: The file exists in `src/admin/` and the function name matches the filename.

### Step 2: Implement page logic

Choose the appropriate pattern based on the page type:

**Pattern A — render_form list page** (for table/list views like `src/admin/paypal_history.php`, `src/admin/paypal_transactions.php`):
```php
add_output(render_form('paypal_history'));
```
Or with parameters:
```php
add_output(render_form('paypal_transactions', ['module' => $GLOBALS['tf']->variables->request['module'], 'acl' => 'client_billing']));
```

**Pattern B — TFTable form page** (for interactive forms like `src/admin/paypal_refund.php`):
```php
$table = new TFTable();
$table->csrf('paypal_refund');
$table->set_title('PayPal Refund');
$table->add_field('Label', 'l');
$table->add_field($table->make_input('field_name', $default, 25), 'l');
$table->add_row();
$table->add_field($table->make_submit('Submit'));
$table->add_row();
add_output($table->get_table());
```

**Pattern C — Database query page** (for detail views like `src/admin/view_paypal_transaction.php`):
```php
$db = clone $GLOBALS['tf']->db;
$db->query("SELECT * FROM table WHERE id = " . intval($GLOBALS['tf']->variables->request['id']), __LINE__, __FILE__);
if ($db->num_rows() > 0) {
    $db->next_record(MYSQL_ASSOC);
    $row = $db->Record;
    // Build output
}
```

**Verify**: The function uses `add_output()` to render content. No direct `echo` statements.

### Step 3: Handle form submissions (if applicable)

If the page processes form input, check for a confirmation variable:
```php
if (isset($GLOBALS['tf']->variables->request['confirmed']) && $GLOBALS['tf']->variables->request['confirmed'] == 'yes') {
    // Process the form
    // Log with myadmin_log('admin', 'info', 'message', __LINE__, __FILE__);
}
```

For database writes, use `make_insert_query()` and log to `history_log`:
```php
$db->query(make_insert_query('history_log', [
    'history_id' => null,
    'history_sid' => $GLOBALS['tf']->session->sessionid,
    'history_timestamp' => mysql_now(),
    'history_creator' => $GLOBALS['tf']->session->account_id,
    'history_owner' => $custid,
    'history_section' => 'paypal_refund',
    'history_type' => $identifier,
    'history_new_value' => $newValue,
    'history_old_value' => $oldValue
]), __LINE__, __FILE__);
```

**Verify**: All `$_GET`/`$_POST` access uses `$GLOBALS['tf']->variables->request['key']`, never raw superglobals.

### Step 4: Register the page in Plugin.php

Open `src/Plugin.php` and add a `$loader->add_page_requirement()` call inside the `getRequirements()` method, grouped with the other page requirements. Follow the path format from existing entries:

```bash
grep 'add_page_requirement' src/Plugin.php  # see existing entries and path format
```

Add the new entry after the existing `add_page_requirement` calls (around line 63 in `src/Plugin.php`).

**Verify**: Run `grep 'add_page_requirement' src/Plugin.php` and confirm the new entry appears alongside existing ones like `paypal_history` and `paypal_refund`.

### Step 5: Add a test (optional but recommended)

Add a test in `tests/` that verifies the function exists and the file is loadable. Follow the pattern in `tests/FileExistenceTest.php`.

**Verify**: Run `vendor/bin/phpunit` and confirm all tests pass.

## Examples

### Example: Simple list page

**User says**: "Add an admin page to view PayPal disputes"

**Actions taken**:

1. Create `src/admin/paypal_disputes.php`:
```php
<?php

    /**
     * Administrative Functionality
     * @author Joe Huss <detain@interserver.net>
     * @copyright 2025
     * @package MyAdmin
     * @category Admin
     */

    /**
     * paypal_disputes()
     *
     */
    function paypal_disputes()
    {
        page_title('PayPal Disputes');
        function_requirements('has_acl');
        if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
            dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
            return false;
        }
        add_output(render_form('paypal_disputes'));
    }
```

2. Add to `src/Plugin.php` `getRequirements()` following the path format from existing entries on lines 60-63.

**Result**: New admin page accessible at `?choice=none.paypal_disputes`, protected by admin ACL.

### Example: Detail view with database query

**User says**: "Create an admin page to view PayPal subscription details"

**Actions taken**:

1. Create `src/admin/view_paypal_subscription.php`:
```php
<?php

    /**
     * Administrative Functionality
     * @author Joe Huss <detain@interserver.net>
     * @copyright 2025
     * @package MyAdmin
     * @category Admin
     */

    function view_paypal_subscription()
    {
        page_title('View PayPal Subscription');
        function_requirements('has_acl');
        if ($GLOBALS['tf']->ima != 'admin' || !has_acl('client_billing')) {
            dialog('Not admin', 'Not Admin or you lack the permissions to view this page.');
            return false;
        }
        if (!isset($GLOBALS['tf']->variables->request['id'])) {
            add_output('Subscription ID is required.');
            return;
        }
        $db = clone $GLOBALS['tf']->db;
        $id = intval($GLOBALS['tf']->variables->request['id']);
        $db->query("SELECT * FROM paypal WHERE paypal_id = {$id}", __LINE__, __FILE__);
        if ($db->num_rows() > 0) {
            $db->next_record(MYSQL_ASSOC);
            $row = $db->Record;
            $table = new TFTable();
            $table->set_title('PayPal Subscription #' . $id);
            foreach ($row as $key => $value) {
                $table->add_field($key, 'r');
                $table->add_field($value, 'l');
                $table->add_row();
            }
            add_output($table->get_table());
        } else {
            add_output('Subscription not found.');
        }
    }
```

2. Register in `src/Plugin.php` `getRequirements()` following the path format from existing entries.

## Common Issues

### `Call to undefined function has_acl()`
You forgot `function_requirements('has_acl');` before calling `has_acl()`. Add it immediately after the `page_title()` call and before the ACL check.

### Page returns blank / no output
The function is not calling `add_output()`. All page content must go through `add_output()` — never use `echo` or `print` directly.

### Page not accessible / 404
The page requirement was not registered in `src/Plugin.php`. Verify:
1. `grep 'paypal_disputes' src/Plugin.php` returns a match
2. The path in `add_page_requirement()` follows the format from existing entries in `src/Plugin.php`
3. The first argument to `add_page_requirement()` matches the function name exactly

### `Undefined index` errors on request variables
Always check with `isset()` before accessing `$GLOBALS['tf']->variables->request['key']`. See `src/admin/paypal_refund.php` line 17 for the pattern.

### Function name collision
Function names are global. Prefix all functions with `paypal_` or `view_paypal_` to avoid collisions with other plugins. Check existing names:
```bash
grep 'add_page_requirement' src/Plugin.php
```

### Database clone pattern
Always use `$db = clone $GLOBALS['tf']->db;` — never assign `$GLOBALS['tf']->db` directly. If you need multiple concurrent queries (e.g., nested loops), create separate clones: `$db = clone $GLOBALS['tf']->db; $dbR = clone $GLOBALS['tf']->db;`
