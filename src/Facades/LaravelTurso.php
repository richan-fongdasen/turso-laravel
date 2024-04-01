<?php

declare(strict_types=1);

namespace RichanFongdasen\LaravelTurso\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RichanFongdasen\LaravelTurso\LaravelTurso
 */
class LaravelTurso extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RichanFongdasen\LaravelTurso\LaravelTurso::class;
    }
}
