<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Queries;

class CloseQuery extends Query
{
    protected static string $type = 'close';

    public function toArray(): array
    {
        return [
            'type' => self::$type,
        ];
    }
}
