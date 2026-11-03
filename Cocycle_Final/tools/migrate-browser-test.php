<?php
// Upgrade only the existing isolated browser-test database; never creates a database.
$runtime = json_decode(file_get_contents(__DIR__.'/../storage/testing/runtime.json'), true, 512, JSON_THROW_ON_ERROR);
if (!preg_match('/^cocycle_test_[a-zA-Z0-9_]+$/', $runtime['database'] ?? '')) throw new RuntimeException('Unsafe test database.');
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.connections.mysql.database' => $runtime['database'], 'database.default' => 'mysql']);
Illuminate\Support\Facades\DB::purge('mysql');
if (Illuminate\Support\Facades\DB::connection()->getDatabaseName() !== $runtime['database']) throw new RuntimeException('Database mismatch.');
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();
