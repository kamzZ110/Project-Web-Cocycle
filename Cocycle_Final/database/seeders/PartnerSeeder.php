<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class PartnerSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Partner User (VIVITOKO)
        $user = User::where('email', 'partner@cocycle.com')->first();
        if (!$user) {
            $user = User::create([
                'username' => 'Vivitoko',
                'email' => 'partner@cocycle.com',
                'password' => Hash::make('password'),
                'role' => 'partner',
                'phone' => '08123456789',
                'address' => 'Jl. Kebun Raya No. 12',
                'fullname' => 'Vivi Owner',
                'nama_toko' => 'VIVITOKO'
            ]);
        } else {
            // Update existing user if needed
            $user->update(['nama_toko' => 'VIVITOKO', 'username' => 'Vivitoko']);
        }

        // 2. Create Store
        $store = Store::updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => 'VIVITOKO',
                'description' => 'Fresh and healthy food store.',
                'phone' => '08123456789',
                'email' => 'vivi@toko.com',
                'address' => 'Jakarta, Indonesia',
                'logo_url' => 'assets/logo_toko.png'
            ]
        );

        // Clear existing products to avoid duplicates during repeated seeding
        Product::where('store_id', $store->id)->delete();

        // 3. Create Products (Matches Screenshots)
        $products = [
            [
                'name' => 'Granola Bar',
                'category' => 'Snack',
                'price' => 24000,
                'stock' => 100,
                'image' => 'assets/granola.jpg', // Placeholder path
                'sold_count' => 481,
                'views_count' => 766,
                'rating' => 4.9
            ],
            [
                'name' => 'Strawberry Smoothie',
                'category' => 'Drink',
                'price' => 14000,
                'stock' => 100,
                'image' => 'assets/strawberry.jpg',
                'sold_count' => 478,
                'views_count' => 671,
                'rating' => 4.7
            ],
            [
                'name' => 'Frozen Raspberry',
                'category' => 'Fruit',
                'price' => 19000,
                'stock' => 50,
                'image' => 'assets/raspberry.jpg',
                'sold_count' => 345,
                'views_count' => 515,
                'rating' => 4.6
            ],
            [
                'name' => 'Lemon Cake',
                'category' => 'Cake',
                'price' => 10000,
                'stock' => 100,
                'image' => 'assets/lemon_cake.jpg',
                'sold_count' => 329,
                'views_count' => 135,
                'rating' => 4.5
            ],
            [
                'name' => 'Red Sweet Pepper',
                'category' => 'Vegetable',
                'price' => 7000,
                'stock' => 100,
                'image' => 'assets/pepper.jpg',
                'sold_count' => 169,
                'views_count' => 853,
                'rating' => 4.8
            ],
            [
                'name' => 'Chocolate Matcha Cake',
                'category' => 'Cake',
                'price' => 16000,
                'stock' => 100,
                'image' => 'assets/matcha.jpg',
                'sold_count' => 233,
                'views_count' => 888,
                'rating' => 4.9
            ]
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, ['store_id' => $store->id]));
        }

        // 4. Dummy Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@cocycle.com'],
            [
                'username' => 'GuestUser',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'fullname' => 'Guest'
            ]
        );

        // 5. Create Orders (Matches Screenshot Order List)
        // Order 1: Preparing (INV-20251221-3259)
        $p1 = Product::where('name', 'Strawberry Smoothie')->first();
        $o1 = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'invoice_number' => 'INV-20251221-3259',
            'total_price' => 55000,
            'status' => 'Preparing',
            'created_at' => Carbon::parse('2025-12-21 21:31:00')
        ]);
        OrderItem::create(['order_id' => $o1->id, 'product_id' => $p1->id, 'quantity' => 2, 'price_per_item' => 14000, 'total_price' => 28000]);

        // Order 2: Preparing (INV-20251221-2263)
         $o2 = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'invoice_number' => 'INV-20251221-2263',
            'total_price' => 2100000,
            'status' => 'Preparing',
            'created_at' => Carbon::parse('2025-12-21 21:26:00')
        ]);
    }
}
