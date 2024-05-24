<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Exceptions\TursoQueryException;
use RichanFongdasen\Turso\Facades\Turso;

test('it can reset client state', function () {
    Turso::resetHttpClientState();

    expect(Turso::getBaseUrl())->toBe('http://127.0.0.1:8080')
        ->and(Turso::getBaton())->toBeNull();
})->group('TursoClient', 'UnitTest');

test('it can log queries', function () {
    fakeHttpRequest();

    $statement = 'SELECT * FROM "users" WHERE "id" = ?';
    $bindings = [
        [
            'type'  => 'integer',
            'value' => 1,
        ],
    ];

    $expectedLog = [
        'request' => [
            'requests' => [
                [
                    'type' => 'execute',
                    'stmt' => [
                        'sql'  => 'PRAGMA foreign_keys = ON;',
                    ],
                ],
                [
                    'type' => 'execute',
                    'stmt' => [
                        'sql'  => $statement,
                        'args' => $bindings,
                    ],
                ],
            ],
        ],
        'response' => [
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
        ],
    ];

    Turso::enableQueryLog();
    Turso::freshHttpRequest();

    Turso::query($statement, $bindings);

    expect(Turso::getQueryLog()->count())->toBe(1)
        ->and(Turso::getQueryLog()->first())->toBe($expectedLog);
})->group('TursoClient', 'UnitTest');

test('it raises exception on any HTTP errors', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Internal Server Error'], 500),
    ]);

    Turso::freshHttpRequest();

    Turso::query('SELECT * FROM "users"');
})->throws(RequestException::class)->group('TursoClient', 'UnitTest');

test('it raises TursoQueryException when the query response has any error in it', function () {
    Http::fake([
        '*' => Http::response(
            [
                'results' => [
                    [
                        'type'  => 'error',
                        'error' => [
                            'code'    => 'QUERY_ERROR',
                            'message' => 'Error: An unknown error has occurred',
                        ],
                    ],
                ],
            ],
            200
        ),
    ]);

    Turso::query('SELECT * FROM "users"');
})->throws(TursoQueryException::class)->group('TursoClient', 'UnitTest');

test('it can replace the base url with the one that suggested by turso response', function () {
    fakeHttpRequest([
        'base_url' => 'http://base-url-example.turso.io',
        'results'  => [
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
    ]);

    Turso::freshHttpRequest();
    Turso::query('SELECT * FROM "users"');

    expect(Turso::getBaseUrl())->toBe('http://base-url-example.turso.io');
})->group('TursoClient', 'UnitTest');

test('it can close the existing http connection', function () {
    fakeHttpRequest([
        'baton'    => 'baton-string-example',
        'base_url' => 'http://base-url-example.turso.io',
        'results'  => [
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
    ]);

    $statement = 'SELECT * FROM "users" WHERE "id" = ?';
    $bindings = [
        [
            'type'  => 'integer',
            'value' => 1,
        ],
    ];

    Turso::query($statement, $bindings);

    expect(Turso::getBaton())->toBe('baton-string-example');

    Turso::close();

    expect(Turso::getBaton())->toBeNull();
})->group('TursoClient', 'UnitTest');
