<?php
// Working reference copy only; the before-framework folder is never changed.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = dirname(base_path()).'/Cocycle (before framework)';
$destination = storage_path('testing/reference');
if (is_dir($destination)) throw new RuntimeException('Reference already exists; refusing overwrite.');
Illuminate\Support\Facades\File::copyDirectory($source, $destination);
$connector = <<<'PHP'
<?php
$base = dirname(__DIR__, 4);
require_once $base.'/vendor/autoload.php';
$app = require $base.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$runtime = json_decode(file_get_contents($base.'/storage/testing/runtime.json'), true);
if (!preg_match('/^cocycle_test_[a-zA-Z0-9_]+$/', $runtime['database'])) throw new RuntimeException('Unsafe reference database.');
$config = config('database.connections.mysql');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $runtime['database'], (int) $config['port']);
$conn->set_charset('utf8mb4');
PHP;
file_put_contents($destination.'/backend/db_connect.php', $connector);
echo "Reference copy ready; database points only to isolated test schema.\n";
