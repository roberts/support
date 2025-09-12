# Common functions used in Laravel packages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/roberts/support.svg?style=flat-square)](https://packagist.org/packages/roberts/support)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/roberts/support/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/roberts/support/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/roberts/support/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/roberts/support/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/roberts/support.svg?style=flat-square)](https://packagist.org/packages/roberts/support)

Traits & Helper functions that are used in multiple Laravel packages & applications.

Test Function
- randomOrCreate

Model Traits
- HasCreator
- HasUpdater

## Installation

You can install the package via composer:

```bash
composer require roberts/support
```

## Usage

On packages or Laravel applications that require this package, you can add these Traits to models:

```php
use HasCreator, HasUpdater;
```

### Expected Columns

When using the `HasCreator` and/or `HasUpdater` traits on a model, add the following nullable columns to your table:

- `creator_id` (unsignedBigInteger, nullable)
- `updater_id` (unsignedBigInteger, nullable)

Example migration snippet:

```php
Schema::table('your_table', function (Blueprint $table) {
	$table->unsignedBigInteger('creator_id')->nullable();
	$table->unsignedBigInteger('updater_id')->nullable();

	// Optional: add foreign keys if your users table is bigint IDs
	// $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
	// $table->foreign('updater_id')->references('id')->on('users')->nullOnDelete();
});
```

The traits automatically:
- Set `creator_id` on model creating (when an authenticated user is present).
- Set `updater_id` on model saving (create and update) when an authenticated user is present.

### Overriding the Auth Provider Model

By default, the traits resolve the related user model from `config('auth.providers.users.model')`.
If your application uses a different provider or model, ensure the config points to your user class. For example, in `config/auth.php`:

```php
'providers' => [
	'users' => [
		'driver' => 'eloquent',
		'model' => App\Models\User::class,
	],
],
```

Or override at runtime (e.g., inside a service provider) if needed:

```php
config(['auth.providers.users.model' => App\Domain\Auth\User::class]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Drew Roberts](https://github.com/drewroberts)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
