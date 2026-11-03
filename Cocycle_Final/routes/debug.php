<?php
use App\Models\Product;
use App\Models\FoodItem;
use Illuminate\Support\Facades\Route;

Route::get('/debug-prices', function() {
    try {
        echo "<h1>Marketplace (Product model)</h1>";
        $p = Product::first(); // Remove constraints
        if ($p) {
            echo "Found Product: ID {$p->id} | Name: {$p->name} | Price: {$p->price} | Status: {$p->status}<br>";
        } else {
            echo "No Products found in DB.<br>";
        }

        echo "<h1>Donation (FoodItem model)</h1>";
        $f = FoodItem::first();
        if ($f) {
            echo "Found FoodItem: ID {$f->id} | Name: {$f->name} | Fee: {$f->handling_fee} | Tags: {$f->tags} | Status: {$f->status}<br>";
        } else {
            echo "No FoodItems found in DB.<br>";
        }
        
        // Show configured DB
        echo "DB Name: " . DB::connection()->getDatabaseName();

    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});
