<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Facades;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\Turso\TursoManager;

/**
 * @see \RichanFongdasen\Turso\TursoHttpClient
 *
 * @mixin \RichanFongdasen\Turso\TursoHttpClient
 */
class Turso extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TursoManager::class;
    }
}
