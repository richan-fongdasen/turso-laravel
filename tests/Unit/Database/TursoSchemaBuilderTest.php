<?php

use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Exceptions\FeatureNotSupportedException;

test('it raises exception on creating a new database.', function () {
    Schema::createDatabase('test');
})->throws(FeatureNotSupportedException::class)->group('TursoSchemaBuilderTest', 'UnitTest');

test('it raises exception on dropping database.', function () {
    Schema::dropDatabaseIfExists('test');
})->throws(FeatureNotSupportedException::class)->group('TursoSchemaBuilderTest', 'UnitTest');
