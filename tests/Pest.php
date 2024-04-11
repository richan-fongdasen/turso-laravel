<?php

use Illuminate\Support\Str;
use RichanFongdasen\Turso\Tests\TestCase;

uses(
    TestCase::class,
)->in(__DIR__);

function migrateTables(...$tableNames): void
{
    collect($tableNames)
        ->each(function (string $tableName) {
            $migration = include __DIR__ . '/Fixtures/Migrations/create_' . Str::snake(Str::plural($tableName)) . '_table.php';
            $migration->up();
        });
}
