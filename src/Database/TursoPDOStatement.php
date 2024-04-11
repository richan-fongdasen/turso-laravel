<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Support\Collection;
use PDO;
use PDOException;
use PDOStatement;
use RichanFongdasen\Turso\Facades\Turso;

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

    protected ?Collection $responses = null;

    public function __construct(
        protected TursoPDO $pdo,
        protected string $query,
        protected array $options = [],
    ) {
        $this->responses = new Collection();
    }

    public function setFetchMode(int $mode, mixed ...$args): bool
    {
        $this->fetchMode = $mode;

        return true;
    }

    public function bindValue(string|int $param, mixed $value, $type = PDO::PARAM_STR): bool
    {
        if ($value === null) {
            $type = PDO::PARAM_NULL;
        }

        $this->bindings[$param] = match ($type) {
            PDO::PARAM_LOB => [
                'type'  => 'blob',
                'value' => base64_encode($value),
            ],
            PDO::PARAM_BOOL => [
                'type'  => 'boolean',
                'value' => (string) ((int) $value),
            ],
            PDO::PARAM_INT => [
                'type'  => 'integer',
                'value' => (string) $value,
            ],
            PDO::PARAM_NULL => [
                'type'  => 'null',
                'value' => 'null',
            ],
            default => [
                'type'  => 'text',
                'value' => (string) $value,
            ],
        };

        return true;
    }

    public function execute(?array $params = null): bool
    {
        collect((array) $params)
            ->each(function (mixed $value, int $key) {
                $type = match (gettype($value)) {
                    'boolean' => PDO::PARAM_BOOL,
                    'double', 'integer' => PDO::PARAM_INT,
                    'resource' => PDO::PARAM_LOB,
                    'NULL'     => PDO::PARAM_NULL,
                    default    => PDO::PARAM_STR,
                };

                $this->bindValue($key, $value, $type);
            });

        $rawResponse = Turso::query($this->query, array_values($this->bindings));
        $this->responses = $this->formatResponse($rawResponse);

        $lastId = (int) data_get($rawResponse, 'result.last_insert_rowid', 0);

        if ($lastId > 0) {
            $this->pdo->setLastInsertId(value: $lastId);
        }

        $this->affectedRows = (int) data_get($rawResponse, 'result.affected_row_count', 0);

        return true;
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        if ($mode === PDO::FETCH_DEFAULT) {
            $mode = $this->fetchMode;
        }

        $response = $this->responses?->shift();

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
        if (! ($this->responses instanceof Collection)) {
            return [];
        }

        if ($mode === PDO::FETCH_DEFAULT) {
            $mode = $this->fetchMode;
        }

        $response = match ($mode) {
            PDO::FETCH_BOTH => $this->responses->map(function (Collection $row) {
                return array_merge($row->toArray(), $row->values()->toArray());
            })->toArray(),
            PDO::FETCH_ASSOC, PDO::FETCH_NAMED => $this->responses->toArray(),
            PDO::FETCH_NUM => $this->responses->map(function (Collection $row) {
                return $row->values()->toArray();
            })->toArray(),
            PDO::FETCH_OBJ => $this->responses->map(function (Collection $row) {
                return (object) $row->toArray();
            })->toArray(),

            default => throw new PDOException('Unsupported fetch mode.'),
        };

        $this->responses = new Collection();

        return $response;
    }

    protected function formatResponse(array $originalResponse): Collection
    {
        $response = new Collection();
        $columns = collect((array) data_get($originalResponse, 'result.cols', []))
            ->keyBy('name')
            ->keys()
            ->all();

        $rows = collect((array) data_get($originalResponse, 'result.rows', []))
            ->each(function (array $item) use (&$response, $columns) {
                $row = new Collection();

                collect($item)
                    ->each(function (array $column, int $index) use (&$row, $columns) {
                        $value = match ($column['type']) {
                            'blob'    => base64_decode((string) $column['value'], true),
                            'integer' => (int) $column['value'],
                            'float'   => (float) $column['value'],
                            'null'    => null,
                            default   => (string) $column['value'],
                        };

                        $row->put(data_get($columns, $index), $value);
                    });

                $response->push($row);
            });

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
        return $this->responses?->count() ?? 0;
    }
}
