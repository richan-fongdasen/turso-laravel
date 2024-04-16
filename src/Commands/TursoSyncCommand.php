<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class TursoSyncCommand extends Command
{
    public $signature = 'turso:sync';

    public $description = 'Sync changes from the remote database to the local replica manually.';

    protected function compileRunProcess(): string
    {
        return sprintf(
            '%s %s "%s" "%s" "%s"',
            $this->getNodePath(),
            config('turso-laravel.sync_command.script_filename'),
            config('database.connections.turso.db_url'),
            config('database.connections.turso.access_token'),
            config('database.connections.turso.db_replica'),
        );
    }

    protected function getNodePath(): string
    {
        $nodePath = config('turso-laravel.sync_command.node_path') ?? trim((string) Process::run('which node')->output());

        if (($nodePath === '') || ! file_exists($nodePath)) {
            throw new RuntimeException('Node executable not found.');
        }

        return $nodePath;
    }

    public function handle(): int
    {
        $timeout = (int) config('turso-laravel.sync_command.timeout');

        $result = Process::timeout($timeout)
            ->path(config('turso-laravel.sync_command.script_path') ?? base_path())
            ->run($this->compileRunProcess());

        if ($result->failed()) {
            throw new RuntimeException('Turso sync command failed: ' . $result->errorOutput());
        }

        $this->info($result->output());

        return self::SUCCESS;
    }
}
