<?php

namespace App\Services;

use App\Models\{User, Store};
use Illuminate\Support\Facades\DB;

class PartnerStarterCatalog
{
    public function initialize(User $account): Store
    {
        return DB::transaction(function () use ($account) {
            $user = User::whereKey($account->id)->lockForUpdate()->firstOrFail();
            if ($user->role !== 'partner') {
                throw new \InvalidArgumentException('Starter catalog is only for partners.');
            }
            $store = $user->store()->firstOrCreate([], [
                'store_name' => mb_substr($user->nama_toko ?: $user->username, 0, 100),
                'address' => $user->address, 'email' => $user->email,
                'phone' => $user->phone, 'status' => 'active',
            ]);
            $store = Store::whereKey($store->id)->lockForUpdate()->firstOrFail();
            if ($store->starter_catalog_initialized_at !== null) return $store;

            // Never add defaults to an existing catalog or a store with historical orders.
            if (!$store->products()->exists() && !$store->orders()->exists()) {
                $products = json_decode(file_get_contents(database_path('data/starter-products.json')), true, 512, JSON_THROW_ON_ERROR);
                foreach ($products as $product) {
                    $store->products()->create($product + ['status' => 'active', 'views_count' => 0, 'sold_count' => 0]);
                }
            }
            $store->update(['starter_catalog_initialized_at' => now()]);
            $account->unsetRelation('store');
            return $store;
        });
    }
}
