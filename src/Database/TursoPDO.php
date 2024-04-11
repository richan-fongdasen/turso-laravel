<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use PDO;
use RichanFongdasen\Turso\Exceptions\FeatureNotSupportedException;

/**
 * Turso PDO Database Connection.
 *
 * This is a custom PDO database connection class
 * that is used by the Turso database driver.
 *
 * see: https://www.php.net/manual/en/class.pdo.php
 */
class TursoPDO extends PDO
{
    protected array $lastInsertIds = [];

    public function __construct(
        string $dsn = 'sqlite::memory:',
        ?string $username = null,
        ?string $password = null,
        ?array $options = null
    ) {
        parent::__construct($dsn, $username, $password, $options);
    }

    public function beginTransaction(): bool
    {
        throw new FeatureNotSupportedException('Database transaction is not supported by the current Turso database driver.');
    }

    public function commit(): bool
    {
        throw new FeatureNotSupportedException('Database transaction is not supported by the current Turso database driver.');
    }

    public function exec(string $queryStatement): int
    {
        $statement = $this->prepare($queryStatement);
        $statement->execute();

        return $statement->getAffectedRows();
    }

    public function inTransaction(): bool
    {
        return false;
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
        throw new FeatureNotSupportedException('Database transaction is not supported by the current Turso database driver.');
    }

    public function setLastInsertId(?string $name = null, ?int $value = null): void
    {
        if ($name === null) {
            $name = 'id';
        }

        $this->lastInsertIds[$name] = $value;
    }
}
