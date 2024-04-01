<?php

declare(strict_types=1);

namespace RichanFongdasen\LaravelTurso\Commands;

use Illuminate\Console\Command;

class LaravelTursoCommand extends Command
{
    public $signature = 'laravel-turso';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
