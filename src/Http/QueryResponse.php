<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Http;

use Illuminate\Support\Collection;
use RichanFongdasen\Turso\Contracts\TursoQuery;
use RichanFongdasen\Turso\Exceptions\TursoQueryException;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

class QueryResponse
{
    protected int $affectedRows = 0;

    protected Collection $columns;

    protected ?string $lastInsertId = null;

    protected TursoQuery $query;

    protected array $rawResponse;

    protected int $replicationIndex = 0;

    protected string $responseType = '';

    protected Collection $rows;

    public function __construct(TursoQuery $query, array $response = [])
    {
        $this->query = $query;
        $this->rawResponse = $response;
        $this->responseType = data_get($response, 'type', 'error');

        $this->affectedRows = (int) data_get($response, 'response.result.affected_row_count', 0);
        $this->lastInsertId = data_get($response, 'response.result.last_insert_rowid');
        $this->replicationIndex = (int) data_get($response, 'response.result.replication_index');

        $this->columns = $this->extractColumns($response);
        $this->rows = $this->extractRows($response);

        $this->checkIfResponseHasError();
    }

    protected function checkIfResponseHasError(): void
    {
        if ($this->responseType !== 'error') {
            return;
        }

        $errorCode = (string) data_get($this->rawResponse, 'error.code', 'UNKNOWN_ERROR');
        $errorMessage = (string) data_get($this->rawResponse, 'error.message', 'Error: An unknown error has occurred');

        $statement = ($this->query instanceof ExecuteQuery)
            ? $this->query->getStatement()
            : $this->query->getType();

        throw new TursoQueryException($errorCode, $errorMessage, $statement);
    }

    protected function extractColumns(array $response): Collection
    {
        return collect((array) data_get($response, 'response.result.cols', []))
            ->keyBy('name')
            ->keys();
    }

    protected function extractRows(array $response): Collection
    {
        $rows = new Collection();

        collect((array) data_get($response, 'response.result.rows', []))
            ->each(function (array $item) use (&$rows) {
                $row = new Collection();

                collect($item)
                    ->each(function (array $column, int $index) use (&$row) {
                        $value = match ($column['type']) {
                            'blob'    => base64_decode((string) base64_decode((string) $column['base64'], true), true),
                            'integer' => (int) $column['value'],
                            'float'   => (float) $column['value'],
                            'null'    => null,
                            default   => (string) $column['value'],
                        };

                        $row->put($this->columns->get($index), $value);
                    });

                $rows->push($row);
            });

        return $rows;
    }

    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    public function getColumns(): Collection
    {
        return $this->columns;
    }

    public function getLastInsertId(): ?string
    {
        return $this->lastInsertId;
    }

    public function getQuery(): TursoQuery
    {
        return $this->query;
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public function getReplicationIndex(): int
    {
        return $this->replicationIndex;
    }

    public function getResponseType(): string
    {
        return $this->responseType;
    }

    public function getRows(): Collection
    {
        return $this->rows;
    }
}
