<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class TursoConnection extends SQLiteConnection
{
    protected bool $hasUpdated = false;

    protected static array $updatingStatements = [
        'alter',
        'create',
        'delete',
        'drop',
        'insert',
        'truncate',
        'update',
    ];

    public function __construct(TursoPDO $pdo, string $database = ':memory:', string $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        if ($this->queryIsUpdatingRemoteDB($query)) {
            $this->hasUpdated = true;
        }

        return parent::affectingStatement($query, $bindings);
    }

    protected function queryIsUpdatingRemoteDB(string $query): bool
    {
        return Str::startsWith(trim(strtolower($query)), self::$updatingStatements);
    }

    /**
     * Get the default query grammar instance.
     */
    protected function getDefaultQueryGrammar(): TursoQueryGrammar
    {
        $grammar = new TursoQueryGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    /**
     * Get a schema builder instance for the connection.
     */
    public function getSchemaBuilder(): TursoSchemaBuilder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new TursoSchemaBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     */
    protected function getDefaultSchemaGrammar(): TursoSchemaGrammar
    {
        $grammar = new TursoSchemaGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    /**
     * Get the schema state for the connection.
     */
    public function getSchemaState(?Filesystem $files = null, ?callable $processFactory = null): TursoSchemaState
    {
        return new TursoSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     */
    protected function getDefaultPostProcessor(): TursoQueryProcessor
    {
        return new TursoQueryProcessor();
    }

    public function hasUpdated(): bool
    {
        return $this->hasUpdated;
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        if ($this->queryIsUpdatingRemoteDB($query)) {
            $this->hasUpdated = true;
        }

        return parent::statement($query, $bindings);
    }
}
