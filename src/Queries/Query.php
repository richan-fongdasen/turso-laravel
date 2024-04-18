<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Queries;

use RichanFongdasen\Turso\Contracts\TursoQuery;

abstract class Query implements TursoQuery
{
    protected int $index = 0;

    protected static string $type = 'QueryType';

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getType(): string
    {
        return static::$type;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function __toString(): string
    {
        return (string) json_encode($this->toArray());
    }

    abstract public function toArray(): array;
}
