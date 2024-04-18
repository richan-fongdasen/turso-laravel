<?php

use RichanFongdasen\Turso\Queries\ExecuteQuery;

beforeEach(function () {
    $this->query = new ExecuteQuery(
        'SELECT * FROM "users" WHERE "id" = ?',
        [
            [
                'type'  => 'integer',
                'value' => 1,
            ],
        ]
    );
});

test('it can returns the query type', function () {
    expect($this->query->getType())->toBe('execute');
})->group('ExecuteQueryTest', 'UnitTest');

test('it can returns the query statement', function () {
    expect($this->query->getStatement())->toBe('SELECT * FROM "users" WHERE "id" = ?');
})->group('ExecuteQueryTest', 'UnitTest');

test('it can returns the query bindings', function () {
    expect($this->query->getBindings())->toBe([
        [
            'type'  => 'integer',
            'value' => 1,
        ],
    ]);
})->group('ExecuteQueryTest', 'UnitTest');

test('it can convert itself into an array', function () {
    expect($this->query->toArray())->toBe([
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
    ]);
})->group('ExecuteQueryTest', 'UnitTest');

test('it can convert itself into a string', function () {
    expect((string) $this->query)->toBe('{"type":"execute","stmt":{"sql":"SELECT * FROM \"users\" WHERE \"id\" = ?","args":[{"type":"integer","value":1}]}}');
})->group('ExecuteQueryTest', 'UnitTest');

test('it can convert itself into an array without any bindings', function () {
    $query = new ExecuteQuery('SELECT * FROM "users"');

    expect($query->toArray())->toBe([
        'type' => 'execute',
        'stmt' => [
            'sql' => 'SELECT * FROM "users"',
        ],
    ]);
})->group('ExecuteQueryTest', 'UnitTest');

test('it can returns the query index', function () {
    expect($this->query->getIndex())->toBe(0);
})->group('ExecuteQueryTest', 'UnitTest');

test('it can set the query index', function () {
    $this->query->setIndex(10);

    expect($this->query->getIndex())->toBe(10);
})->group('ExecuteQueryTest', 'UnitTest');
