# A Turso database driver for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/richan-fongdasen/turso-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/turso-laravel)
[![License: MIT](https://poser.pugx.org/richan-fongdasen/turso-laravel/license.svg)](https://opensource.org/licenses/MIT)
[![Unit Tests](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/run-tests.yml)
[![Code Style](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/fix-php-code-style-issues.yml)
[![codecov](https://codecov.io/gh/richan-fongdasen/turso-laravel/graph/badge.svg?token=eKJSttyUGc)](https://codecov.io/gh/richan-fongdasen/turso-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/richan-fongdasen/turso-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/turso-laravel)

This package provides a Turso database driver for Laravel. It allows you to use Turso database as your database driver in Laravel application. The database driver is implemented using HTTP client to communicate with the Turso database server.

## Unsupported Features

There are some features that are not supported by this package yet. Here are the list of unsupported features:

-   Creating and dropping database
-   [Database Transactions](https://turso.tech/blog/bring-your-own-sdk-with-tursos-http-api-ff4ccbed)
-   [Turso Batch Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#execute-a-batch)
-   [Turso Sequence Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#execute-a-sequence-of-sql-statements)
-   [Turso Describe Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#describe-a-statement)
-   [Turso Store SQL Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#store-an-sql-text-on-the-server)
-   [Turso Close Stored SQL Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#close-a-stored-sql-text)

## Requirements

-   PHP 8.2 or higher
-   Laravel 11.0 or higher

## Installation

You can install the package via composer:

```bash
composer require richan-fongdasen/turso-laravel
```

To use Turso as your database driver in Laravel, you need to append the following configuration to the `connections` array in your `config/database.php` file:

```php
'turso' => [
    'driver'                  => 'turso',
    'turso_url'               => env('DB_URL', 'http://localhost:8080'),
    'database'                => null,
    'prefix'                  => env('DB_PREFIX', ''),
    'access_token'            => env('DB_ACCESS_TOKEN', null),
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

## Configuration

In Laravel application, The database driver configuration is stored in your `.env` file. Here is the list of available configuration for Turso database driver:

```bash
DB_CONNECTION=turso
DB_URL=http://localhost:8080
DB_PREFIX=
DB_ACCESS_TOKEN=
```

## Usage

For local development, you can use the local Turso database server that is provided by the Turso database team for development purposes. You can find the instruction to run the local Turso database server in the [Turso CLI documentation](https://docs.turso.tech/local-development#turso-cli).

The Turso database driver should work as expected with Laravel Query Builder and Eloquent ORM.

## Debugging

There is a way to debug the HTTP request and response that is sent and received by the Turso database client. Here is the example of how to enable the debugging feature:

```php
Turso::enableQueryLog();

DB::table('users')->get();

// Get the query log
$logs = Turso::getQueryLog();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Richan Fongdasen](https://github.com/richan-fongdasen)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
