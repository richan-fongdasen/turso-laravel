<?php

use Illuminate\Support\Facades\Http;
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

function fakeHttpRequest(array $response = []): void
{
    if ($response === []) {
        $response = [
            'results' => [
                [
                    'type'     => 'ok',
                    'response' => [
                        'result' => [
                            'affected_row_count' => 1,
                            'last_insert_rowid'  => '1',
                            'replication_index'  => 0,
                        ],
                    ],
                ],
                [
                    'type'     => 'ok',
                    'response' => [
                        'result' => [
                            'affected_row_count' => 1,
                            'last_insert_rowid'  => '1',
                            'replication_index'  => 0,
                        ],
                    ],
                ],
            ],
        ];
    }

    Http::fake([
        '*' => Http::response($response),
    ]);
}
