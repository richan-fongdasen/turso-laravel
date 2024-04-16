<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDO;
use RichanFongdasen\Turso\Jobs\TursoSyncJob;

class TursoManager
{
    protected TursoHttpClient $client;

    protected Collection $config;

    protected ?PDO $readPdo = null;

    public function __construct(array $config = [])
    {
        $this->config = new Collection($config);
        $this->client = new TursoHttpClient($config);
    }

    public function backgroundSync(): void
    {
        if ($this->config->get('db_replica', false) !== false) {
            TursoSyncJob::dispatch();
        }
    }

    public function disableReadReplica(): bool
    {
        $this->readPdo = DB::connection('turso')->getReadPdo();

        DB::connection('turso')->setReadPdo(null);

        return true;
    }

    public function enableReadReplica(): bool
    {
        if ($this->readPdo === null) {
            return false;
        }

        DB::connection('turso')->setReadPdo($this->readPdo);

        return true;
    }

    public function sync(): void
    {
        if ($this->config->get('db_replica', false) !== false) {
            Artisan::call('turso:sync');
        }
    }

    public function __call(string $method, array $arguments = []): mixed
    {
        if (! method_exists($this->client, $method)) {
            throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
        }

        return $this->client->$method(...$arguments);
    }
}
