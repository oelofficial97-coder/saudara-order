<?php

declare(strict_types=1);

// Normalize SCRIPT_NAME and PHP_SELF so Laravel generates correct root-relative URLs on Vercel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Prepare ephemeral writable storage in /tmp for Vercel Serverless Function runtime
$storagePath = getenv('LARAVEL_STORAGE_PATH') ?: '/tmp/storage';

$requiredDirectories = [
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    $storagePath.'/app/private',
    $storagePath.'/app/public',
    $storagePath.'/certs',
];

foreach ($requiredDirectories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

$targetCa = $storagePath.'/certs/ca.pem';
if (! file_exists($targetCa)) {
    $sourceCa = __DIR__.'/../database/certs/ca.pem';
    if (file_exists($sourceCa)) {
        copy($sourceCa, $targetCa);
    }
}

// Forward serverless request to Laravel's front controller
require __DIR__.'/../public/index.php';
