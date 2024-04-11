<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Facades\Turso;

it('can test', function () {
    expect(true)->toBeTrue();

    // $grammar = DB::getSchemaGrammar();
    // dd($grammar->compileDropAllTables());

    $query = <<<'END'
        CREATE TABLE admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name char(255),
            email char(255),
            password char(255),
            remember_token char(100),
            deleted_at timestamp NULL DEFAULT NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL
        )
        END;

    // Turso::query($query);

    // dd(Turso::query("SELECT * FROM sqlite_schema WHERE type='table' AND NAME NOT LIKE 'sqlite_%'"));

    $query2 = <<<'END'
            INSERT INTO `admins` VALUES (2,'Richan','richan@technovative.co.id','mypassword',NULL,NULL,'2022-05-15 10:28:30','2022-05-15 10:28:30');
        END;

    // dd(Http::tursoQuery($query2)->json(), $query2);
    // dd(Http::tursoQuery('SELECT * FROM admins')->json());

    // dd(Turso::query($query2));
});
