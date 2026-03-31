# MyAdmin PayPal Payments Plugin

PayPal payment processing plugin for the MyAdmin billing system. Namespace: `Detain\MyAdminPaypal\` → `src/`.

## Commands

```bash
composer install                    # install dependencies
vendor/bin/phpunit                  # run all tests
vendor/bin/phpunit tests/PluginTest.php  # run single test file
vendor/bin/phpunit --coverage-text  # tests with coverage report
```

## Architecture

**Entry point**: `src/Plugin.php` — registers hooks via `getHooks()` returning `system.settings` → `getSettings()` and `function.requirements` → `getRequirements()`

**Core classes**:
- `src/PayPalCheckout.php` — Express Checkout & Digital Goods via NVP API, sandbox/live toggle via `$sandboxFlag`, endpoints `$liveApiEndpoint` / `$sandboxApiEndpoint`
- `src/Plugin.php` — Symfony `EventDispatcher` hook registration, settings (constants `PAYPAL_EMAIL`, `PAYPAL_API_USERNAME`, `PAYPAL_API_PASSWORD`, `PAYPAL_API_SIGNATURE`, etc.)

**Procedural functions**:
- `src/paypal.functions.inc.php` — IPN variable getters (`get_paypal_buyer_information_vars()`, `get_paypal_payment_information_vars()`, etc.), payment links (`get_paypal_link_url()`, `get_paypal_subscription_link_url()`), refund check (`is_paypal_txn_refunded()`)
- `src/paypal_refund.functions.php` — `PayPalHttpPost()` NVP API caller, `refundPaypalTransaction()` full/partial refund logic

**Admin pages** (`src/admin/`):
- `view_paypal_transaction.php` — transaction detail viewer with `get_paypal_transaction_types()`, `get_paypal_cats_and_fields()`
- `paypal_history.php` — payment history via `render_form('paypal_history')`
- `paypal_transactions.php` — transaction list via `render_form('paypal_transactions')`
- `paypal_refund.php` — refund UI with partial/full refund, invoice updates via `MyAdmin\Orm\Invoice`

**IPN variable schemas** (`src/info/*.json`): 12 JSON files mapping PayPal IPN fields — `paypal_transaction_types.json`, `paypal_payment_info_variables.json`, `paypal_subscription_variables.json`, `paypal_recurring_payment_variables.json`, etc.

**Utility scripts** (`bin/`):
- `paypal_drop_columns.php` — generates ALTER TABLE to drop migrated columns
- `paypal_get_field_usage_counts.php` — counts non-null field usage in `paypal` table
- `paypal_map_fields_to_extra.php` — migrates column data into JSON `extra` field

## Testing

- **Framework**: PHPUnit 9 · config `phpunit.xml.dist` · bootstrap `tests/bootstrap.php`
- **Bootstrap**: defines stub constants (`PAYPAL_ENABLE`, `PAYPAL_EMAIL`, `PAYPAL_API_USERNAME`, etc.) and creates config stub at `src/../../../../include/config/config.settings.php`
- **Test files**: `tests/PluginTest.php`, `tests/PayPalCheckoutTest.php`, `tests/PaypalFunctionsTest.php`, `tests/PaypalRefundFunctionsTest.php`, `tests/ViewPaypalTransactionTest.php`, `tests/FileExistenceTest.php`

```bash
vendor/bin/phpunit --testdox tests/  # run tests with readable output
```

## Conventions

- Namespace `Detain\MyAdminPaypal` PSR-4 mapped to `src/` in `composer.json`
- Admin pages check `$GLOBALS['tf']->ima == 'admin'` and `has_acl('client_billing')` before proceeding
- Database: use `get_module_db($module)`, `$db->query()`, `$db->real_escape()`, `make_insert_query()` — never PDO
- Logging: `myadmin_log('billing', 'info', $message, __LINE__, __FILE__)`
- Plugin hooks registered as static methods on `Plugin` class, dispatched via Symfony `GenericEvent`
- Settings use constants like `PAYPAL_EMAIL`, `PAYPAL_API_USERNAME`, `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`
- PayPal API calls use NVP format via cURL in `PayPalHttpPost()` — response parsed from `&`-delimited string
- Custom field compression: values > 200 chars compressed with `gzcompress()` + `base64_encode()`, prefixed `COMPRESSED`
- Commit messages: lowercase, descriptive
- License: LGPL-2.1

## Dependencies

- PHP >= 7.4 · extensions: `curl`, `mbstring`, `soap`
- `symfony/event-dispatcher` ^5.0|^6.0|^7.0
- `detain/myadmin-plugin-installer` (custom Composer installer)
- Dev: `phpunit/phpunit` ^9.6

## CI

- `.scrutinizer.yml` — static analysis, coverage via clover
- `.travis.yml` — legacy CI (PHP 5.4-7.1)
- GitHub Actions: `tests.yml` workflow

```bash
composer validate --strict  # validate composer.json
```

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md .agents/ .opencode/ 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->

<!-- caliber:managed:sync -->
## Context Sync

This project uses [Caliber](https://github.com/caliber-ai-org/ai-setup) to keep AI agent configs in sync across Claude Code, Cursor, Copilot, and Codex.
Configs update automatically before each commit via `caliber refresh`.
If the pre-commit hook is not set up, run `/setup-caliber` to configure everything automatically.
<!-- /caliber:managed:sync -->
