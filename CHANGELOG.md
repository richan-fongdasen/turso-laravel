# Changelog

All notable changes to `richan-fongdasen/turso-laravel` will be documented in this file.

## 1.0.0 - 2024-11-13

### What's Changed

* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/richan-fongdasen/turso-laravel/pull/9
* Added support for multiple Turso database connections in https://github.com/richan-fongdasen/turso-laravel/commit/3b446e39ea247760347c1d6324c7c58e0aedc207
* Updated Github actions workflow to use docker compose v2 in https://github.com/richan-fongdasen/turso-laravel/commit/fd8aad61eb484a4d52846564c4d8d40e0d7f906b
* Fixed the artisan command migrate:fresh error in https://github.com/richan-fongdasen/turso-laravel/commit/c7067356a618534eef80fdc3a74374c3b22aeb64
* Limited the baton token lifetime to 8 seconds in https://github.com/richan-fongdasen/turso-laravel/commit/1e089c909883ae529e871d7b2988f093405cc893
* Fixed embedded replica sync issue in https://github.com/richan-fongdasen/turso-laravel/commit/a5c16232cc8a37ce5799953446a881a0aa9412a6
* Changed libsql:// protocol to https:// protocol in database URLs automatically in https://github.com/richan-fongdasen/turso-laravel/commit/0ce0a8bd1116f316d2fd1f11cc0f496320bc67e4
* Removed unused `database` value from Turso database configuration in https://github.com/richan-fongdasen/turso-laravel/commit/1380f947c667a0a355b7769a6736197aa6a3851a

**Full Changelog**: https://github.com/richan-fongdasen/turso-laravel/compare/0.7.0...1.0.0

## 0.7.0 - 2024-04-29

Make the previously hardcoded values to be configurable.

## 0.6.1 - 2024-04-23

* Added a [quick fix for the binary/blob data](https://github.com/richan-fongdasen/turso-laravel/pull/4) issue.
* Added tests related to supported database column types.
* Added tests related to the Eloquent attribute casting feature.
* Fixed errors found by the added test cases.

## 0.6.0 - 2024-04-22

Enable database transactions feature.

## 0.5.0 - 2024-04-20

Dropped several features which are already provided by the Laravel framework.

## 0.4.0 - 2024-04-18

* Calling `Turso::sync()` without specifying the `DB_REPLICA` path raises an exception.
* The `TursoConnection` class now inherits directly from `Illuminate\Database\Connection`.
* Completed major code refactoring.

## 0.3.0 - 2024-04-16

* Fixed an error that occurred when dropping database indexes.
* Prevented the creation of a PDO object for the replica database if the path to the replica database is not set or empty.
* Automatically synced the replica database after running an Artisan command that updates the remote database (e.g., migration command).
* Fixed broken tests.
* Manually tested the replica database sync script.

## 0.2.0 - 2024-04-14

* Add support for Turso's embedded replica feature.
* Update readme.

## 0.1.0 - 2024-04-11

**Alpha release**

Note: This release has been manually tested on a small project's development environment.
