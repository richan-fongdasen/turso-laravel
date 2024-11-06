<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Database;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;

class TursoConnector extends Connector implements ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @return \RichanFongdasen\Turso\Database\TursoPDO
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        return new TursoPDO($config, $options);
    }
}
