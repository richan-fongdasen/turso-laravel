<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Filesystem\Filesystem;

class TursoConnection extends SQLiteConnection
{
    protected bool $hasUpdated = false;

    public function __construct(TursoPDO $pdo, string $database = ':memory:', string $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
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

    /**
     * Run an insert statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        $this->hasUpdated = true;

        return parent::insert($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function update($query, $bindings = [])
    {
        $this->hasUpdated = true;

        return parent::update($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        $this->hasUpdated = true;

        return parent::delete($query, $bindings);
    }

    public function hasUpdated(): bool
    {
        return $this->hasUpdated;
    }
}
