<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Project;

beforeEach(function () {
    config()->set('database.connections.otherdb', [
        'driver'                  => 'turso',
        'db_url'                  => env('DB_URL', 'http://127.0.0.1:8080'),
        'db_replica'              => env('DB_REPLICA'),
        'database'                => null,
        'prefix'                  => env('DB_PREFIX', ''),
        'access_token'            => 'your-access-token',
        'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        'sticky'                  => env('DB_STICKY', true),
    ]);

    migrateTables('projects');

    $this->project1 = Project::factory()->create();
    $this->project2 = Project::factory()->create();
    $this->project3 = Project::factory()->create();
});

afterEach(function () {
    Schema::dropAllTables();
});

test('each connection has its own turso client instance', function () {
    $client1 = DB::connection('turso')->getPdo()->getClient();
    $client2 = DB::connection('otherdb')->getPdo()->getClient();

    expect($client1)->not->toBe($client2);
})->group('MultiConnectionsTest', 'FeatureTest');

test('it can get all rows from the projects table through the otherdb connection', function () {
    $projects = DB::connection('otherdb')->table('projects')->get();

    expect($projects)->toHaveCount(3)
        ->and($projects[0]->name)->toEqual($this->project1->name)
        ->and($projects[1]->name)->toEqual($this->project2->name)
        ->and($projects[2]->name)->toEqual($this->project3->name);
})->group('MultiConnectionsTest', 'FeatureTest');
