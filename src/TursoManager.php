<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use LogicException;
use RichanFongdasen\Turso\Jobs\TursoSyncJob;

class TursoManager
{
    protected TursoClient $client;

    protected Collection $config;

    public function __construct(array $config = [])
    {
        $this->config = new Collection($config);
        $this->client = new TursoClient($config);
    }

    public function backgroundSync(): void
    {
        if ((string) $this->config->get('db_replica') === '') {
            throw new LogicException('Turso Error: You cannot sync the data when the read replica is not enabled.');
        }

        TursoSyncJob::dispatch();
    }

    public function sync(): void
    {
        if ((string) $this->config->get('db_replica') === '') {
            throw new LogicException('Turso Error: You cannot sync the data when the read replica is not enabled.');
        }

        Artisan::call('turso:sync');

        DB::forgetRecordModificationState();
    }

    public function __call(string $method, array $arguments = []): mixed
    {
        if (! method_exists($this->client, $method)) {
            throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
        }

        return $this->client->$method(...$arguments);
    }
}
