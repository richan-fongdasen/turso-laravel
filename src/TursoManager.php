<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use LogicException;
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
        if ((string) $this->config->get('db_replica') === '') {
            throw new LogicException('Turso Error: You cannot sync the data when the read replica is not enabled.');
        }

        TursoSyncJob::dispatch();
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
        if ((string) $this->config->get('db_replica') === '') {
            throw new LogicException('Turso Error: You cannot sync the data when the read replica is not enabled.');
        }

        Artisan::call('turso:sync');

        $this->enableReadReplica();
    }

    public function __call(string $method, array $arguments = []): mixed
    {
        if (! method_exists($this->client, $method)) {
            throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
        }

        return $this->client->$method(...$arguments);
    }
}
