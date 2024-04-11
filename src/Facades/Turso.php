<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Facades;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\Turso\TursoClient;

/**
 * @see \RichanFongdasen\Turso\TursoClient
 *
 * @mixin \RichanFongdasen\Turso\TursoClient
 */
class Turso extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TursoClient::class;
    }
}
