<?php
// Creates a NEW, uniquely named test database. Never deletes a database or existing rows.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\{DB, Artisan};
use App\Models\{User, Product, FoodItem};

if (DB::connection()->getDriverName() !== 'mysql') throw new RuntimeException('MySQL required.');
$source = DB::connection()->getDatabaseName();
$target = 'cocycle_test_'.date('Ymd_His').'_'.bin2hex(random_bytes(3));
$business = ['users', 'stores', 'products', 'orders', 'order_items', 'cart', 'communities', 'food_items', 'donations', 'donation_details', 'impact_trackings'];
$definitions = [];
if (!in_array('--empty-schema', $argv, true)) {
    foreach ($business as $table) {
        $result = (array) DB::selectOne("SHOW CREATE TABLE `$table`");
        $definitions[] = $result['Create Table'];
    }
}
DB::statement("CREATE DATABASE `$target` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
config(['database.connections.mysql.database' => $target]);
DB::purge('mysql');
if (DB::connection()->getDatabaseName() !== $target || !str_starts_with($target, 'cocycle_test_')) throw new RuntimeException('Test guard failed.');
DB::statement('SET FOREIGN_KEY_CHECKS=0');
foreach ($definitions as $definition) DB::statement($definition);
DB::statement('SET FOREIGN_KEY_CHECKS=1');
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();
if (in_array('--empty-schema', $argv, true)) {
    echo "Empty MySQL installation verified: $target\n";
    exit;
}
$password = 'CoCycleTest123!';
$customer = User::create(['username' => 'Browser Customer', 'email' => 'customer@example.test', 'phone' => '08123456789', 'password' => $password, 'role' => 'customer', 'address' => 'Alamat uji']);
$partner = User::create(['username' => 'Browser Partner', 'email' => 'partner@example.test', 'phone' => '08123456780', 'password' => $password, 'role' => 'partner', 'nama_toko' => 'Toko Uji CoCycle', 'address' => 'Alamat uji']);
$store = $partner->store()->create(['store_name' => 'Toko Uji CoCycle', 'status' => 'active']);
$originalConfig = config('database.connections.mysql');
$originalConfig['database'] = $source;
config(['database.connections.reference_source' => $originalConfig]);
$products = DB::connection('reference_source')->table('products')->get();
foreach ($products as $product) {
    $attributes = (array) $product;
    unset($attributes['id']);
    $attributes['store_id'] = $store->id;
    Product::create($attributes);
}
FoodItem::create(['store_name' => 'Toko Uji CoCycle', 'name' => 'Roti Donasi Uji', 'image_url' => 'assets/roti.jpg', 'quantity_available' => 20, 'unit' => 'pcs', 'handling_fee' => 3000, 'tags' => '["Makanan"]', 'status' => 'available']);
if (!is_dir(storage_path('testing'))) mkdir(storage_path('testing'), 0777, true);
file_put_contents(storage_path('testing/runtime.json'), json_encode(['database' => $target, 'customer_id' => $customer->id, 'partner_id' => $partner->id], JSON_PRETTY_PRINT));
echo "Browser test schema prepared: $target (no original user records copied).\n";
