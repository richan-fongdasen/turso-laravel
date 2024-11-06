<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Queries;

class ExecuteQuery extends Query
{
    protected static string $type = 'execute';

    public function __construct(
        protected string $statement,
        protected array $bindings = []
    ) {}

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function getStatement(): string
    {
        return $this->statement;
    }

    public function toArray(): array
    {
        $arrayValue = [
            'type' => static::$type,
            'stmt' => [
                'sql' => $this->statement,
            ],
        ];

        if ($this->bindings !== []) {
            $arrayValue['stmt']['args'] = $this->bindings;
        }

        return $arrayValue;
    }
}
