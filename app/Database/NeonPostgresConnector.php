<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;
use InvalidArgumentException;

class NeonPostgresConnector extends PostgresConnector
{
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);
        $host = $config['host'] ?? null;

        if (! is_string($host) || ! str_ends_with($host, '.neon.tech')) {
            return $dsn;
        }

        $endpoint = explode('.', $host, 2)[0];

        if (! is_string($endpoint) || ! preg_match('/^ep-[a-z0-9-]+$/', $endpoint)) {
            throw new InvalidArgumentException('Unable to determine the Neon endpoint ID from the PostgreSQL host.');
        }

        return "{$dsn};options='endpoint={$endpoint}'";
    }
}
