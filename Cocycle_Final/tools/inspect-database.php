<?php
// Read-only schema and aggregate audit. Never prints credentials or user records.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
echo 'Driver: '.$db->getDriverName().PHP_EOL;
foreach (Illuminate\Support\Facades\Schema::getTables($db->getDatabaseName()) as $table) {
    $name = $table['name'];
    echo $name.': '.$db->table($name)->count().' rows'.PHP_EOL;
    foreach (Illuminate\Support\Facades\Schema::getColumns($name) as $column) {
        echo '  '.$column['name'].' '.$column['type'].' nullable='.(int)$column['nullable'].PHP_EOL;
    }
    echo '  foreign keys: '.json_encode(Illuminate\Support\Facades\Schema::getForeignKeys($name)).PHP_EOL;
}
echo 'Partners without store: '.App\Models\User::where('role', 'partner')->doesntHave('store')->count().PHP_EOL;
echo 'Stores owned by non-partners: '.App\Models\Store::whereHas('user', fn ($q) => $q->where('role', '!=', 'partner'))->count().PHP_EOL;
