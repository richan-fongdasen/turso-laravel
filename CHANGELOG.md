# Changelog

All notable changes to `richan-fongdasen/turso-laravel` will be documented in this file.

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
