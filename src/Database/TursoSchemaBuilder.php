<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Database\Schema\SQLiteBuilder;
use RichanFongdasen\Turso\Exceptions\FeatureNotSupportedException;

class TursoSchemaBuilder extends SQLiteBuilder
{
    public function createDatabase($name)
    {
        throw new FeatureNotSupportedException('Creating database is not supported in Turso database.');
    }

    public function dropDatabaseIfExists($name)
    {
        throw new FeatureNotSupportedException('Dropping database is not supported in Turso database.');
    }

    protected function dropAllIndexes(): void
    {
        $statement = $this->connection->getPdo()->prepare($this->grammar()->compileDropAllIndexes());
        $statement->execute();

        collect($statement->fetchAll(\PDO::FETCH_NUM))->each(function (array $query) {
            $this->connection->statement($query[0]);
        });
    }

    public function dropAllTables(): void
    {
        $this->dropAllTriggers();
        $this->dropAllIndexes();

        $this->connection->statement($this->grammar()->compileDisableForeignKeyConstraints());

        $statement = $this->connection->getPdo()->prepare($this->grammar()->compileDropAllTables());
        $statement->execute();

        collect($statement->fetchAll(\PDO::FETCH_NUM))->each(function (array $query) {
            $this->connection->statement($query[0]);
        });

        $this->connection->statement($this->grammar()->compileEnableForeignKeyConstraints());
    }

    protected function dropAllTriggers(): void
    {
        $statement = $this->connection->getPdo()->prepare($this->grammar()->compileDropAllTriggers());
        $statement->execute();

        collect($statement->fetchAll(\PDO::FETCH_NUM))->each(function (array $query) {
            $this->connection->statement($query[0]);
        });
    }

    public function dropAllViews(): void
    {
        $statement = $this->connection->getPdo()->prepare($this->grammar()->compileDropAllViews());
        $statement->execute();

        collect($statement->fetchAll(\PDO::FETCH_NUM))->each(function (array $query) {
            $this->connection->statement($query[0]);
        });
    }

    protected function grammar(): TursoSchemaGrammar
    {
        if (! ($this->grammar instanceof TursoSchemaGrammar)) {
            $this->grammar = new TursoSchemaGrammar();
        }

        return $this->grammar;
    }
}
