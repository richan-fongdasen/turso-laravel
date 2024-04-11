<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

afterEach(function () {
    Schema::dropAllTables();
    Schema::dropAllViews();
});

test('it can drops all tables from the database.', function () {
    DB::statement('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');

    Schema::dropAllTables();

    $response = DB::select('SELECT * FROM sqlite_schema WHERE type = ? AND name NOT LIKE ?', ['table', 'sqlite_%']);

    expect($response)->toBe([]);
})->group('TursoSchemaBuilderTest', 'FeatureTest');

test('it can retrieve all of the table information in the database', function () {
    DB::select('CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)');

    $result = Schema::getTables()[0];

    expect($result['name'])->toBe('migrations')
        ->and($result['schema'])->toBeNull()
        ->and($result['comment'])->toBeNull()
        ->and($result['collation'])->toBeNull()
        ->and($result['engine'])->toBeNull();
})->group('TursoSchemaBuilderTest', 'FeatureTest');

test('it can retrieve all of the column information in the table', function () {
    DB::select('CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null)');

    $result = collect(Schema::getColumns('migrations'))->keyBy('name');

    expect($result->count())->toBe(3)
        ->and($result->has('id'))->toBeTrue()
        ->and($result->has('migration'))->toBeTrue()
        ->and($result->has('batch'))->toBeTrue()
        ->and($result->get('id'))->toBe([
            'name'           => 'id',
            'type_name'      => 'integer',
            'type'           => 'integer',
            'collation'      => null,
            'nullable'       => false,
            'default'        => null,
            'auto_increment' => true,
            'comment'        => null,
            'generation'     => null,
        ])
        ->and($result->get('migration'))->toBe([
            'name'           => 'migration',
            'type_name'      => 'varchar',
            'type'           => 'varchar',
            'collation'      => null,
            'nullable'       => false,
            'default'        => null,
            'auto_increment' => false,
            'comment'        => null,
            'generation'     => null,
        ])
        ->and($result->get('batch'))->toBe([
            'name'           => 'batch',
            'type_name'      => 'integer',
            'type'           => 'integer',
            'collation'      => null,
            'nullable'       => false,
            'default'        => null,
            'auto_increment' => false,
            'comment'        => null,
            'generation'     => null,
        ]);
})->group('TursoSchemaBuilderTest', 'FeatureTest');

test('it can create a new table', function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
    });

    $result = Schema::getTables()[0];

    expect($result['name'])->toBe('users')
        ->and($result['schema'])->toBeNull()
        ->and($result['comment'])->toBeNull()
        ->and($result['collation'])->toBeNull()
        ->and($result['engine'])->toBeNull();

    $columns = collect(Schema::getColumns('users'))->keyBy('name')->keys()->all();

    expect($columns)->toBe(['id', 'name']);
})->group('TursoSchemaBuilderTest', 'FeatureTest');

test('it can alter an existing table.', function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
    });

    Schema::table('users', function (Blueprint $table) {
        $table->string('email')->after('name');
    });

    expect(Schema::hasColumn('users', 'email'))->toBeTrue()
        ->and(Schema::hasColumns('users', ['id', 'name', 'email']))->toBeTrue()
        ->and(Schema::getColumnType('users', 'email'))->toBe('varchar')
        ->and(Schema::getColumnListing('users'))->toBe(['id', 'name', 'email']);
})->group('TursoSchemaBuilderTest', 'FeatureTest');

test('it can drop all views from the database', function () {
    $createSql = 'CREATE VIEW foo (id) AS SELECT 1';

    DB::statement($createSql);

    $view = collect(Schema::getViews())->first();

    expect($view['name'])->toBe('foo')
        ->and($view['schema'])->toBeNull()
        ->and($view['definition'])->toBe($createSql);

    Schema::dropAllViews();

    expect(Schema::getViews())->toBe([]);
})->group('TursoSchemaBuilderTest', 'FeatureTest');
