<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class TursoSyncCommand extends Command
{
    public $signature = 'turso:sync';

    public $description = 'Sync changes from the remote database to the local replica manually.';

    protected function compileRunProcess(): string
    {
        return sprintf(
            'node %s "%s" "%s" "%s"',
            config('turso-laravel.sync.script_filename'),
            config('database.connections.turso.db_url'),
            config('database.connections.turso.access_token'),
            config('database.connections.turso.db_replica'),
        );
    }

    public function handle(): int
    {
        $timeout = (int) config('turso-laravel.sync.timeout');

        $result = Process::timeout($timeout)
            ->path(config('turso-laravel.sync.script_path') ?? base_path())
            ->run($this->compileRunProcess());

        if ($result->failed()) {
            $this->error($result->errorOutput());

            return self::FAILURE;
        }

        $this->info($result->output());

        return self::SUCCESS;
    }
}
