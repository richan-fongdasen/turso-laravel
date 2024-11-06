<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Http;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RichanFongdasen\Turso\Contracts\TursoQuery;
use RichanFongdasen\Turso\Database\TursoSchemaGrammar;
use RichanFongdasen\Turso\Queries\CloseQuery;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

class RequestBody implements Arrayable
{
    protected ?string $baton;

    protected Collection $queries;

    protected bool $shouldClose = false;

    public function __construct(?string $baton = null)
    {
        $this->baton = $baton;
        $this->queries = new Collection();
    }

    public static function create(?string $baton = null): self
    {
        return new RequestBody($baton);
    }

    public function clearQueries(): self
    {
        $this->queries = new Collection();

        return $this;
    }

    public function getQuery(int $index): TursoQuery
    {
        if (! $this->queries->has($index)) {
            throw new InvalidArgumentException('Can not find the TursoQuery instance with the specified index: ' . $index . '.');
        }

        return $this->queries->get($index);
    }

    public function push(TursoQuery $query): self
    {
        $this->queries->push($query);

        $query->setIndex($this->queries->count() - 1);

        return $this;
    }

    public function withCloseRequest(): self
    {
        $this->shouldClose = true;

        return $this;
    }

    public function withoutCloseRequest(): self
    {
        $this->shouldClose = false;

        return $this;
    }

    public function withForeignKeyConstraints(bool $constraintsEnabled): self
    {
        // Make sure that the foreign key constraints statement
        // is getting executed only once.
        if ((string) $this->baton !== '') {
            return $this;
        }

        $grammar = app(TursoSchemaGrammar::class);

        $statement = $constraintsEnabled
            ? $grammar->compileEnableForeignKeyConstraints()
            : $grammar->compileDisableForeignKeyConstraints();

        $this->push(new ExecuteQuery($statement));

        return $this;
    }

    public function toArray(): array
    {
        $body = [];

        if ((string) $this->baton !== '') {
            $body['baton'] = $this->baton;
        }

        $body['requests'] = $this->queries->toArray();

        if ($this->shouldClose) {
            $body['requests'][] = (new CloseQuery())->toArray();
        }

        return $body;
    }
}
