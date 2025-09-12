# Roberts Support Laravel Package

Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the information here.

This is a Laravel 12.25+ package providing common traits and helper functions used across multiple Laravel applications. The package provides `HasCreator` and `HasUpdater` traits for automatically tracking user IDs on model operations, plus a `randomOrCreate()` test helper function.

## Working Effectively

**Bootstrap and validate the repository:**
- Requires PHP 8.4+ (check with `php --version`)
- `composer install --no-interaction` -- installs all dependencies in ~44 seconds
- If PHP 8.4 unavailable, use `composer install --ignore-platform-reqs --no-interaction` to install with warnings
- `composer test` -- runs Pest test suite with 9 tests in ~1.3 seconds. NEVER CANCEL.
- `composer analyse` -- runs PHPStan static analysis in ~3.6 seconds. NEVER CANCEL.
- `composer format` -- runs Laravel Pint code formatting in ~1 second. NEVER CANCEL.

**CRITICAL VALIDATION RULE:** All commands complete in under 60 seconds. Set timeout to 120 seconds minimum for all commands.

**Key validation steps - run after ANY change:**
- ALWAYS run `composer test` after making changes - if tests fail, your change broke something
- ALWAYS run `composer format` and `composer analyse` before committing - CI will fail otherwise

## Repository Structure

**Core source files (src/):**
- `Traits/HasCreator.php` -- Automatically sets `creator_id` on model creation
- `Traits/HasUpdater.php` -- Automatically sets `updater_id` on model save 
- `Helpers/Test.php` -- Contains `randomOrCreate()` helper function
- `Support.php` and `SupportServiceProvider.php` -- Laravel package setup

**Tests (tests/):**
- Uses Pest testing framework with Orchestra Testbench for Laravel package testing
- `Feature/HasCreatorTest.php` and `Feature/HasUpdaterTest.php` -- trait tests
- `Unit/HelpersTest.php` -- helper function tests
- `ArchTest.php` -- architecture tests preventing debug function usage
- `Fixtures/` -- test models (User, Post) with factories

## Manual Validation Scenarios

**ALWAYS test these scenarios after making changes:**

1. **HasCreator trait validation:**
   - Create authenticated session, create model, verify `creator_id` is set
   - Create unauthenticated session, create model, verify `creator_id` is null

2. **HasUpdater trait validation:**
   - Create model with authenticated user, verify `updater_id` is set
   - Update model with different authenticated user, verify `updater_id` changes

3. **randomOrCreate() helper validation:**
   - Call with existing models, verify returns random existing model
   - Call with empty table, verify creates new model using factory
   - Call with model instance, verify works correctly

## Common Tasks and Commands

**Testing:**
- `vendor/bin/pest` -- run test suite directly (~1 second)
- `vendor/bin/pest --ci` -- run tests in CI mode (as used by GitHub Actions)  
- `vendor/bin/pest tests/Feature/HasCreatorTest.php` -- run specific test file
- `composer test-coverage` -- attempts coverage but fails due to Xdebug config

**Static analysis:**
- `vendor/bin/phpstan analyse` -- run PHPStan directly (~3.6 seconds)
- `vendor/bin/phpstan --error-format=github` -- run with GitHub Actions format
- Uses level 5 analysis with baseline in `phpstan-baseline.neon`

**Code formatting:**
- `vendor/bin/pint` -- run Laravel Pint directly (~1 second)
- Follows Laravel code style standards

**Package management:**
- `composer require roberts/support` -- install in another Laravel project
- `composer update --with-dependencies` -- update this package dependencies

## Model Trait Usage

When using `HasCreator` and/or `HasUpdater` traits:
- Add nullable columns: `creator_id` and `updater_id` (unsignedBigInteger, nullable)
- Traits automatically resolve user model from `config('auth.providers.users.model')`
- `HasCreator` sets `creator_id` on model `creating` event when user authenticated
- `HasUpdater` sets `updater_id` on model `saving` event when user authenticated

**Example usage:**
```php
use Roberts\Support\Traits\HasCreator;
use Roberts\Support\Traits\HasUpdater;

class YourModel extends Model 
{
    use HasCreator, HasUpdater;
}
```

## GitHub Actions CI Pipeline

The CI runs on PHP 8.4 with Laravel 12.25 on Ubuntu and Windows:
- **Tests:** Run with `vendor/bin/pest --ci` (timeout: 5 minutes)
- **Code style:** Auto-fixes with Laravel Pint and commits changes  
- **PHPStan:** Runs static analysis with GitHub error format (timeout: 5 minutes)
- **Dependabot:** Weekly updates for Composer and GitHub Actions dependencies

## Important Notes

- Package requires PHP 8.4+ and Laravel 12.25+ in production
- Uses Spatie Laravel Package Tools for package scaffolding
- All timing estimates based on validation runs - actual performance may vary
- Total codebase: 6 source files, 10 test files, ~51 PHP files total excluding vendor
- Package is published on Packagist as `roberts/support`
- MIT licensed with Drew Roberts as primary maintainer