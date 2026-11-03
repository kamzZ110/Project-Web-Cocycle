<?php
// One-time safe setup; repeatable without duplicating products. Never imports or deletes data.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\{DB, Artisan};

$snapshots = [];
foreach (['users', 'products', 'orders', 'order_items', 'stores'] as $table) {
    $snapshots[$table] = DB::table($table)->orderBy('id')->get()->map(function ($row) use ($table) {
        $values = (array) $row;
        if ($table === 'stores') unset($values['updated_at'], $values['starter_catalog_initialized_at']);
        return $values;
    })->all();
}
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();
Artisan::call('cocycle:starter-products');
echo Artisan::output();
foreach ($snapshots as $table => $rows) {
    foreach ($rows as $before) {
        $after = (array) DB::table($table)->where('id', $before['id'])->first();
        if ($table === 'stores') unset($after['updated_at'], $after['starter_catalog_initialized_at']);
        if ($before !== $after) throw new RuntimeException('Original row changed: '.$table.' ID '.$before['id']);
    }
    echo $table.': '.count($rows).' original rows unchanged; '.DB::table($table)->count().' total now.'.PHP_EOL;
}
