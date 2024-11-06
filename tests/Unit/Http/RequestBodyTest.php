<?php

use RichanFongdasen\Turso\Http\RequestBody;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

beforeEach(function () {
    $this->baton = null;
    $this->request = RequestBody::create($this->baton)
        ->withCloseRequest()
        ->withForeignKeyConstraints(true)
        ->push(new ExecuteQuery('SELECT * FROM "users"'));
});

test('it can convert itself into an array', function () {
    expect($this->request->toArray())->toBe([
        'requests' => [
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'PRAGMA foreign_keys = ON;',
                ],
            ],
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'SELECT * FROM "users"',
                ],
            ],
            [
                'type' => 'close',
            ],
        ],
    ]);
})->group('RequestBodyTest', 'UnitTest');

test('it can clear all queries', function () {
    $this->request->clearQueries();

    expect($this->request->toArray())->toBe([
        'requests' => [
            [
                'type' => 'close',
            ],
        ],
    ]);
})->group('RequestBodyTest', 'UnitTest');

test('it can push a new query', function () {
    $this->request->push(new ExecuteQuery('SELECT * FROM "users" WHERE "id" = ?', [
        [
            'type'  => 'integer',
            'value' => 1,
        ],
    ]));

    expect($this->request->toArray())->toBe([
        'requests' => [
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'PRAGMA foreign_keys = ON;',
                ],
            ],
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'SELECT * FROM "users"',
                ],
            ],
            [
                'type' => 'execute',
                'stmt' => [
                    'sql'  => 'SELECT * FROM "users" WHERE "id" = ?',
                    'args' => [
                        [
                            'type'  => 'integer',
                            'value' => 1,
                        ],
                    ],
                ],
            ],
            [
                'type' => 'close',
            ],
        ],
    ]);
})->group('RequestBodyTest', 'UnitTest');

test('it can remove the close query from the body', function () {
    $this->request->withoutCloseRequest();

    expect($this->request->toArray())->toBe([
        'requests' => [
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'PRAGMA foreign_keys = ON;',
                ],
            ],
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'SELECT * FROM "users"',
                ],
            ],
        ],
    ]);
})->group('RequestBodyTest', 'UnitTest');

test('it can convert itself into an array with baton value being set', function () {
    $this->request = RequestBody::create('some-baton-string')
        ->withCloseRequest()
        ->withForeignKeyConstraints(true)
        ->push(new ExecuteQuery('SELECT * FROM "users"'));

    expect($this->request->toArray())->toBe([
        'baton'    => 'some-baton-string',
        'requests' => [
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => 'SELECT * FROM "users"',
                ],
            ],
            [
                'type' => 'close',
            ],
        ],
    ]);
})->group('RequestBodyTest', 'UnitTest');

test('it can retrieve a specific TursoQuery instance by the given index', function () {
    $query = $this->request->getQuery(1);

    expect($query)->toBeInstanceOf(ExecuteQuery::class)
        ->and($query->getType())->toBe('execute')
        ->and($query->getIndex())->toBe(1)
        ->and($query->getStatement())->toBe('SELECT * FROM "users"')
        ->and($query->getBindings())->toBe([]);
})->group('RequestBodyTest', 'UnitTest');

test('it raises InvalidArgumentException when the query index is not found', function () {
    $this->request->getQuery(8);
})->throws(\InvalidArgumentException::class)->group('RequestBodyTest', 'UnitTest');
