<?php

// Pastikan direktori writeable di /tmp tersedia untuk Laravel cache & views
$storageDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

require __DIR__.'/../public/index.php';
