# Changelog

All notable changes to `richan-fongdasen/turso-laravel` will be documented in this file.

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
