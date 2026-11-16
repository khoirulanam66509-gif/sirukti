<?php

// Pastikan direktori sementara di /tmp tersedia pada environment serverless Vercel.
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/fonts',
] as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
