<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Exceptions\TursoQueryException;
use RichanFongdasen\Turso\TursoClient;

beforeEach(function () {
    $this->client = new TursoClient(config('database.connections.turso'));
});

test('it can reset client state', function () {
    $this->client->resetHttpClientState();

    expect($this->client->getBaseUrl())->toBe('http://127.0.0.1:8080')
        ->and($this->client->getBaton())->toBeNull();
})->group('TursoClientTest', 'UnitTest');

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

    $this->client->enableQueryLog();
    $this->client->freshHttpRequest();

    $this->client->query($statement, $bindings);

    expect($this->client->getQueryLog()->count())->toBe(1)
        ->and($this->client->getQueryLog()->first())->toBe($expectedLog);
})->group('TursoClientTest', 'UnitTest');

test('it can flush the query log', function () {
    fakeHttpRequest();

    $statement = 'SELECT * FROM "users" WHERE "id" = ?';
    $bindings = [
        [
            'type'  => 'integer',
            'value' => 1,
        ],
    ];

    $this->client->enableQueryLog();
    $this->client->freshHttpRequest();

    $this->client->query($statement, $bindings);

    $this->client->flushQueryLog();

    expect($this->client->getQueryLog()->count())->toBe(0)
        ->and($this->client->getQueryLog()->first())->toBeNull();
})->group('TursoClientTest', 'UnitTest');

test('it raises exception on any HTTP errors', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Internal Server Error'], 500),
    ]);

    $this->client->freshHttpRequest();

    $this->client->query('SELECT * FROM "users"');
})->throws(RequestException::class)->group('TursoClientTest', 'UnitTest');

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

    $this->client->query('SELECT * FROM "users"');
})->throws(TursoQueryException::class)->group('TursoClientTest', 'UnitTest');

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

    $this->client->freshHttpRequest();
    $this->client->query('SELECT * FROM "users"');

    expect($this->client->getBaseUrl())->toBe('http://base-url-example.turso.io');
})->group('TursoClientTest', 'UnitTest');
