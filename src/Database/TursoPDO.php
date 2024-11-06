<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use PDO;
use RichanFongdasen\Turso\TursoClient;

/**
 * Turso PDO Database Connection.
 *
 * This is a custom PDO database connection class
 * that is used by the Turso database driver.
 *
 * see: https://www.php.net/manual/en/class.pdo.php
 *
 * Turso database transactions & interactive queries reference:
 * https://docs.turso.tech/sdk/http/reference#interactive-query
 */
class TursoPDO extends PDO
{
    protected TursoClient $client;

    protected array $config = [];

    protected bool $inTransaction = false;

    protected array $lastInsertIds = [];

    public function __construct(
        array $config,
        ?array $options = null
    ) {
        parent::__construct('sqlite::memory:', null, null, $options);

        $this->config = $config;
        $this->client = new TursoClient($config);
    }

    public function beginTransaction(): bool
    {
        $this->inTransaction = $this->prepare('BEGIN')->execute();

        return $this->inTransaction;
    }

    public function commit(): bool
    {
        $result = $this->prepare('COMMIT')->execute();

        $this->inTransaction = false;

        return $result;
    }

    public function exec(string $queryStatement): int
    {
        $statement = $this->prepare($queryStatement);
        $statement->execute();

        return $statement->rowCount();
    }

    public function getClient(): TursoClient
    {
        return $this->client;
    }

    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        if ($name === null) {
            $name = 'id';
        }

        return (isset($this->lastInsertIds[$name]))
            ? (string) $this->lastInsertIds[$name]
            : false;
    }

    public function prepare(string $query, array $options = []): TursoPDOStatement
    {
        return new TursoPDOStatement($this, $query, $options);
    }

    public function rollBack(): bool
    {
        $result = $this->prepare('ROLLBACK')->execute();

        $this->inTransaction = false;

        return $result;
    }

    public function setLastInsertId(?string $name = null, ?int $value = null): void
    {
        if ($name === null) {
            $name = 'id';
        }

        $this->lastInsertIds[$name] = $value;
    }
}
