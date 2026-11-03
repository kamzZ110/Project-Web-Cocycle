<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PartnerWalletTransaction;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PartnerWallet
{
    public const PARTNER_SHARE = 0.90;

    /** Credit completed legacy orders once, then keep the existing users.balance in sync. */
    public function reconcile(User $user): User
    {
        $store = Store::where('user_id', $user->id)->first();
        if (!$store) return $user->fresh();

        return DB::transaction(function () use ($user, $store) {
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $completedOrders = Order::where('store_id', $store->id)->where('status', 'Completed')->get();

            foreach ($completedOrders as $order) {
                $inserted = DB::table('partner_wallet_transactions')->insertOrIgnore([
                    'user_id' => $lockedUser->id,
                    'order_id' => $order->id,
                    'type' => 'income',
                    'amount' => round((float) $order->total_price * self::PARTNER_SHARE, 2),
                    'status' => 'confirmed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($inserted) {
                    $lockedUser->balance = (float) $lockedUser->balance + round((float) $order->total_price * self::PARTNER_SHARE, 2);
                }
            }

            $lockedUser->save();
            return $lockedUser->fresh();
        });
    }

    /** Credit an order as it changes to Completed; the unique order_id prevents duplicates. */
    public function creditCompletedOrder(Order $order, User $user): void
    {
        $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
        $amount = round((float) $order->total_price * self::PARTNER_SHARE, 2);
        $inserted = DB::table('partner_wallet_transactions')->insertOrIgnore([
            'user_id' => $lockedUser->id,
            'order_id' => $order->id,
            'type' => 'income',
            'amount' => $amount,
            'status' => 'confirmed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted) {
            $lockedUser->balance = (float) $lockedUser->balance + $amount;
            $lockedUser->save();
        }
    }
}
