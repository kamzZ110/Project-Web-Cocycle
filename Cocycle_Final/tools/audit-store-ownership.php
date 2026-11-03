<?php
// Read-only audit: no passwords, email addresses, or user data are modified.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
echo 'Database: '.$db->getDatabaseName().PHP_EOL;
foreach (['users' => ['id', 'username', 'role', 'nama_toko'],
    'stores' => ['id', 'user_id', 'store_name', 'status'],
    'products' => ['id', 'store_id', 'name', 'stock', 'status'],
    'orders' => ['id', 'user_id', 'store_id', 'status'],
] as $table => $columns) {
    echo $table.': '.json_encode($db->table($table)->select($columns)->orderBy('id')->get(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
}
