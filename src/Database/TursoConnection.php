<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Exception;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PDO;
use RichanFongdasen\Turso\Jobs\TursoSyncJob;

class TursoConnection extends Connection
{
    public function __construct(TursoPDO $pdo, string $database = ':memory:', string $tablePrefix = '', array $config = [])
    {
        if (isset($config['db_url']) && Str::startsWith($config['db_url'], 'libsql:')) {
            $config['db_url'] = Str::replaceFirst('libsql:', 'https:', $config['db_url']);
        }

        parent::__construct($pdo, $database, $tablePrefix, $config);

        $this->schemaGrammar = $this->getDefaultSchemaGrammar();
    }

    public function createReadPdo(array $config = []): ?PDO
    {
        $replicaPath = (string) data_get($config, 'db_replica');

        if (($replicaPath === '') || ! file_exists($replicaPath)) {
            return null;
        }

        $pdo = new PDO('sqlite:' . $replicaPath);

        $this->setReadPdo($pdo);

        return $pdo;
    }

    protected function escapeBinary(mixed $value): string
    {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    protected function getDefaultPostProcessor(): TursoQueryProcessor
    {
        return new TursoQueryProcessor();
    }

    protected function getDefaultQueryGrammar(): TursoQueryGrammar
    {
        $grammar = new TursoQueryGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    protected function getDefaultSchemaGrammar(): TursoSchemaGrammar
    {
        $grammar = new TursoSchemaGrammar();
        $grammar->setConnection($this);

        $this->withTablePrefix($grammar);

        return $grammar;
    }

    public function getSchemaBuilder(): TursoSchemaBuilder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new TursoSchemaBuilder($this);
    }

    public function getSchemaState(?Filesystem $files = null, ?callable $processFactory = null): TursoSchemaState
    {
        return new TursoSchemaState($this, $files, $processFactory);
    }

    protected function isUniqueConstraintError(Exception $exception): bool
    {
        return boolval(preg_match('#(column(s)? .* (is|are) not unique|UNIQUE constraint failed: .*)#i', $exception->getMessage()));
    }

    public function sync(): void
    {
        Artisan::call('turso:sync', ['connectionName' => $this->getName()]);
    }

    public function backgroundSync(): void
    {
        TursoSyncJob::dispatch((string) $this->getName());
        $this->enableQueryLog();
    }

    public function disableQueryLog(): void
    {
        parent::disableQueryLog();

        $this->tursoPdo()->getClient()->disableQueryLog();
    }

    public function enableQueryLog(): void
    {
        parent::enableQueryLog();

        $this->tursoPdo()->getClient()->enableQueryLog();
    }

    public function flushQueryLog(): void
    {
        parent::flushQueryLog();

        $this->tursoPdo()->getClient()->flushQueryLog();
    }

    public function getQueryLog()
    {
        return $this->tursoPdo()->getClient()->getQueryLog()->toArray();
    }

    public function tursoPdo(): TursoPDO
    {
        if (! $this->pdo instanceof TursoPDO) {
            throw new Exception('The current PDO instance is not an instance of TursoPDO.');
        }

        return $this->pdo;
    }
}
