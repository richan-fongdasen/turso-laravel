<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use Override;

class TursoSchemaGrammar extends SQLiteGrammar
{
    public function compileDropAllIndexes(): string
    {
        return "SELECT 'DROP INDEX IF EXISTS \"' || name || '\";' FROM sqlite_schema WHERE type = 'index'";
    }

    public function compileDropAllTables(): string
    {
        return "SELECT 'DROP TABLE IF EXISTS \"' || name || '\";' FROM sqlite_schema WHERE type = 'table' AND name NOT LIKE 'sqlite_%'";
    }

    public function compileDropAllTriggers(): string
    {
        return "SELECT 'DROP TRIGGER IF EXISTS \"' || name || '\";' FROM sqlite_schema WHERE type = 'trigger'";
    }

    public function compileDropAllViews(): string
    {
        return "SELECT 'DROP VIEW IF EXISTS \"' || name || '\";' FROM sqlite_schema WHERE type = 'view'";
    }

    #[Override]
    public function wrap($value, $prefixAlias = false): string
    {
        return str_replace('"', '\'', parent::wrap($value, $prefixAlias));
    }
}
