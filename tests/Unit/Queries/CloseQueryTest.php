<?php

use RichanFongdasen\Turso\Queries\CloseQuery;

beforeEach(function () {
    $this->query = new CloseQuery();
});

test('it can returns the query type', function () {
    expect($this->query->getType())->toBe('close');
})->group('CloseQueryTest', 'UnitTest');

test('it can convert itself into an array', function () {
    expect($this->query->toArray())->toBe([
        'type' => 'close',
    ]);
})->group('CloseQueryTest', 'UnitTest');

test('it can convert itself into a string', function () {
    expect((string) $this->query)->toBe('{"type":"close"}');
})->group('CloseQueryTest', 'UnitTest');

test('it can returns the query index', function () {
    expect($this->query->getIndex())->toBe(0);
})->group('CloseQueryTest', 'UnitTest');

test('it can set the query index', function () {
    $this->query->setIndex(10);

    expect($this->query->getIndex())->toBe(10);
})->group('CloseQueryTest', 'UnitTest');
