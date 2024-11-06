<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Support\Collection;
use PDO;
use PDOException;
use PDOStatement;
use RichanFongdasen\Turso\Enums\PdoParam;
use RichanFongdasen\Turso\Enums\TursoType;
use RichanFongdasen\Turso\Http\QueryResponse;

/**
 * Turso PDO Statement.
 *
 * This is a custom PDO statement class that is used by the Turso database driver.
 * see: https://www.php.net/manual/en/class.pdostatement.php
 */
class TursoPDOStatement extends PDOStatement
{
    protected int $affectedRows = 0;

    protected int $fetchMode = PDO::FETCH_BOTH;

    protected array $bindings = [];

    protected ?QueryResponse $response = null;

    public function __construct(
        protected TursoPDO $pdo,
        protected string $query,
        protected array $options = [],
    ) {}

    public function setFetchMode(int $mode, mixed ...$args): bool
    {
        $this->fetchMode = $mode;

        return true;
    }

    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
        $type = TursoType::fromValue($value);

        $this->bindings[$param] = $type->bind($value);

        return true;
    }

    public function execute(?array $params = null): bool
    {
        collect((array) $params)
            ->each(function (mixed $value, int $key) {
                $type = PdoParam::fromValue($value);

                $this->bindValue($key, $value, $type->value);
            });

        $this->response = $this->pdo->getClient()->query($this->query, array_values($this->bindings));

        $lastId = (int) $this->response->getLastInsertId();
        if ($lastId > 0) {
            $this->pdo->setLastInsertId(value: $lastId);
        }

        $this->affectedRows = $this->response->getAffectedRows();

        return true;
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        if ($mode === PDO::FETCH_DEFAULT) {
            $mode = $this->fetchMode;
        }

        $response = $this->response?->getRows()->shift();

        if ($response === null) {
            return false;
        }

        return match ($mode) {
            PDO::FETCH_BOTH => array_merge(
                $response->toArray(),
                $response->values()->toArray()
            ),
            PDO::FETCH_ASSOC, PDO::FETCH_NAMED => $response->toArray(),
            PDO::FETCH_NUM => $response->values()->toArray(),
            PDO::FETCH_OBJ => (object) $response->toArray(),

            default => throw new PDOException('Unsupported fetch mode.'),
        };
    }

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, ...$args): array
    {
        if (! ($this->response instanceof QueryResponse)) {
            return [];
        }

        if ($mode === PDO::FETCH_DEFAULT) {
            $mode = $this->fetchMode;
        }

        $allRows = $this->response->getRows();

        $response = match ($mode) {
            PDO::FETCH_BOTH => $allRows->map(function (Collection $row) {
                return array_merge($row->toArray(), $row->values()->toArray());
            })->toArray(),
            PDO::FETCH_ASSOC, PDO::FETCH_NAMED => $allRows->toArray(),
            PDO::FETCH_NUM => $allRows->map(function (Collection $row) {
                return $row->values()->toArray();
            })->toArray(),
            PDO::FETCH_OBJ => $allRows->map(function (Collection $row) {
                return (object) $row->toArray();
            })->toArray(),

            default => throw new PDOException('Unsupported fetch mode.'),
        };

        return $response;
    }

    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    public function nextRowset(): bool
    {
        // TODO: Make sure if Turso database support multiple rowset.
        return false;
    }

    public function rowCount(): int
    {
        return max((int) $this->response?->getRows()->count(), $this->affectedRows);
    }
}
