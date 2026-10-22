<?php

// Pastikan direktori writeable di /tmp tersedia untuk Laravel cache & views
$storageDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/database',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Inisialisasi SQLite database di /tmp jika koneksi DB_CONNECTION adalah sqlite (atau belum di-set)
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? null);
if (!$dbConnection || $dbConnection === 'sqlite') {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    $dbPath = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? '/tmp/database.sqlite');
    
    // Pastikan path database berada di /tmp karena /var/task di Vercel adalah read-only
    if (!str_starts_with($dbPath, '/tmp/')) {
        $dbPath = '/tmp/database.sqlite';
    }

    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;

    if (!file_exists($dbPath)) {
        $sourceDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($sourceDb)) {
            @copy($sourceDb, $dbPath);
        } else {
            @touch($dbPath);
        }
    }
}

require __DIR__.'/../public/index.php';
