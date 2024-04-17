<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Exception;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PDO;

class TursoConnection extends Connection
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

    public function createReadPdo(array $config = []): ?PDO
    {
        $replicaPath = (string) data_get($config, 'db_replica');

        if (($replicaPath === '') || ! file_exists($replicaPath)) {
            return null;
        }

        $pdo = new PDO('sqlite:' . $replicaPath);

        $this->setReadPdo($pdo);

        return $pdo;
    }

    protected function escapeBinary(mixed $value): string
    {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    protected function getDefaultPostProcessor(): TursoQueryProcessor
    {
        return new TursoQueryProcessor();
    }

    protected function getDefaultQueryGrammar(): TursoQueryGrammar
    {
        $grammar = new TursoQueryGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    protected function getDefaultSchemaGrammar(): TursoSchemaGrammar
    {
        $grammar = new TursoSchemaGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    public function getSchemaBuilder(): TursoSchemaBuilder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new TursoSchemaBuilder($this);
    }

    public function getSchemaState(?Filesystem $files = null, ?callable $processFactory = null): TursoSchemaState
    {
        return new TursoSchemaState($this, $files, $processFactory);
    }

    public function hasUpdated(): bool
    {
        return $this->hasUpdated;
    }

    protected function isUniqueConstraintError(Exception $exception): bool
    {
        return boolval(preg_match('#(column(s)? .* (is|are) not unique|UNIQUE constraint failed: .*)#i', $exception->getMessage()));
    }

    protected function queryIsUpdatingRemoteDB(string $query): bool
    {
        return Str::startsWith(trim(strtolower($query)), self::$updatingStatements);
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
