<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class TursoSyncCommand extends Command
{
    public $signature = 'turso:sync {connectionName?}';

    public $description = 'Sync changes from the remote database to the local replica manually.';

    protected function compileRunProcess(string $connectionName): string
    {
        return sprintf(
            '%s %s "%s" "%s" "%s"',
            $this->getNodePath(),
            config('turso-laravel.sync_command.script_filename'),
            DB::connection($connectionName)->getConfig('db_url'),
            DB::connection($connectionName)->getConfig('access_token'),
            DB::connection($connectionName)->getConfig('db_replica'),
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

        $connectionName = $this->argument('connectionName') ?? DB::getDefaultConnection();

        if (DB::connection($connectionName)->getConfig('driver') !== 'turso') {
            $this->error('The specified connection is not a Turso connection.');

            return self::FAILURE;
        }

        if ((string) DB::connection($connectionName)->getConfig('db_replica') === '') {
            $this->error('The specified connection does not have a read replica.');

            return self::FAILURE;
        }

        $result = Process::timeout($timeout)
            ->path(config('turso-laravel.sync_command.script_path') ?? base_path())
            ->run($this->compileRunProcess($connectionName));

        if ($result->failed()) {
            throw new RuntimeException('Turso sync command failed: ' . $result->errorOutput());
        }

        $this->info($result->output());

        DB::connection($connectionName)->forgetRecordModificationState();

        return self::SUCCESS;
    }
}
