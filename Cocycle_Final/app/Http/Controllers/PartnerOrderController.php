<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class PartnerOrderController extends Controller
{
    private function getStore()
    {
        return Store::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $store = $this->getStore();
        if (!$store) abort(404, 'Store not found');

        $orders = Order::with(['user', 'items'])
            ->where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partner.orders.index', compact('orders', 'store'));
    }

    public function show($id)
    {
        $store = $this->getStore();
        if (!$store) abort(404, 'Store not found');

        $order = Order::with(['user', 'items.product'])
            ->where('store_id', $store->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('partner.orders.show', compact('order', 'store'));
    }

    public function updateStatus(Request $request, \App\Services\PartnerWallet $wallet)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:Preparing,Ready,Completed,Cancelled'
        ]);

        $store = $this->getStore();
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $store, $wallet) {
        $order = Order::lockForUpdate()->where('id', $request->order_id)
            ->where('store_id', $store->id)
            ->firstOrFail();

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found or unauthorized']);
        }

        if ($order->status === $request->status) return response()->json(['status' => 'success']);
        $allowed = ['Preparing' => ['Ready', 'Cancelled'], 'Ready' => ['Completed', 'Cancelled']];
        if (!in_array($request->status, $allowed[$order->status] ?? [], true)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'Perubahan status tidak diizinkan.']);
        }
        $order->status = $request->status;
        if ($order->status === 'Completed') $order->completed_at = now();
        if ($order->status === 'Cancelled') {
            $order->cancelled_at = now();
            if ($order->stock_reserved) {
                foreach ($order->items()->orderBy('product_id')->get() as $item) {
                    $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                    if (!$product) continue;
                    $product->stock += $item->quantity;
                    $product->sold_count = max(0, $product->sold_count - $item->quantity);
                    if ($product->status === 'out_of_stock') $product->status = 'active';
                    $product->save();
                }
                $order->stock_reserved = false;
            }
        }
        $order->save();

        if ($order->status === 'Completed') {
            $wallet->creditCompletedOrder($order, $store->user);
        }

        return response()->json(['status' => 'success']);
        });
    }
}
