<?php

use App\Database\NeonPostgresConnector;

it('passes the Neon endpoint ID in the PostgreSQL connection options', function (string $host, string $endpoint) {
    $connector = new class extends NeonPostgresConnector
    {
        public function dsn(array $config): string
        {
            return $this->getDsn($config);
        }
    };

    expect($connector->dsn([
        'host' => $host,
        'database' => 'neondb',
        'port' => '5432',
        'sslmode' => 'require',
    ]))->toContain(";options='endpoint={$endpoint}'");
})->with([
    'pooled host' => ['ep-broad-union-b74i9o3g-pooler.c-13.us-east-1.aws.neon.tech', 'ep-broad-union-b74i9o3g-pooler'],
    'direct host' => ['ep-broad-union-b74i9o3g.c-13.us-east-1.aws.neon.tech', 'ep-broad-union-b74i9o3g'],
]);

it('does not add Neon options to non-Neon PostgreSQL hosts', function () {
    $connector = new class extends NeonPostgresConnector
    {
        public function dsn(array $config): string
        {
            return $this->getDsn($config);
        }
    };

    expect($connector->dsn([
        'host' => 'db.example.com',
        'database' => 'app',
        'port' => '5432',
    ]))->not->toContain('options=');
});
