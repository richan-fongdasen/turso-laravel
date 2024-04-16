<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Exceptions\TursoQueryException;
use RichanFongdasen\Turso\Facades\Turso;

test('it can reset client state', function () {
    Turso::resetClientState();

    expect(Turso::getBaseUrl())->toBe('http://127.0.0.1:8080')
        ->and(Turso::getBaton())->toBeNull();
})->group('TursoClient', 'UnitTest');

test('it can log queries', function () {
    Http::fake();

    $query = [
        'statement' => 'SELECT * FROM "users" WHERE "id" = ?',
        'bindings'  => [
            [
                'type'  => 'integer',
                'value' => 1,
            ],
        ],
        'response' => null,
    ];

    Turso::enableQueryLog();
    Turso::freshRequest();

    Turso::query($query['statement'], $query['bindings']);

    expect(Turso::getQueryLog()->count())->toBe(1)
        ->and(Turso::getQueryLog()->first())->toBe($query);
})->group('TursoClient', 'UnitTest');

test('it raises exception on any HTTP errors', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Internal Server Error'], 500),
    ]);

    Turso::freshRequest();

    Turso::query('SELECT * FROM "users"');
})->throws(RequestException::class)->group('TursoClient', 'UnitTest');

test('it raises TursoQueryException when the query response has any response in it', function () {
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
    Http::fake([
        '*' => Http::response(
            [
                'base_url' => 'http://base-url-example.turso.io',
            ],
            200
        ),
    ]);

    Turso::freshRequest();
    Turso::query('SELECT * FROM "users"');

    expect(Turso::getBaseUrl())->toBe('http://base-url-example.turso.io');
})->group('TursoClient', 'UnitTest');
