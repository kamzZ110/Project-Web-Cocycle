<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Product, Order, OrderItem, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        $cart_items = Cart::with('product.store.user')->where('user_id', Auth::id())->get();
        return view('cart.index', compact('cart_items'));
    }

    private function available(Product $product, int $quantity): void
    {
        if (!$product->store || $product->store->user?->role !== 'partner') {
            throw ValidationException::withMessages(['cart' => 'Produk '.$product->name.' di keranjang berasal dari toko lama yang belum terhubung ke akun partner. Hapus item ini lalu pilih kembali produk dan toko dari marketplace.']);
        }
        if ($product->status !== 'active' || $product->store->status !== 'active' || $quantity < 1 || $quantity > $product->stock) {
            throw ValidationException::withMessages(['qty' => 'Produk tidak tersedia atau jumlah melebihi stok.']);
        }
    }

    public function add(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|integer|exists:products,id']);
        DB::transaction(function () use ($data) {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $cart = Cart::firstOrNew(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $quantity = ($cart->exists ? $cart->quantity : 0) + 1;
            $this->available($product, $quantity);
            $cart->fill(['product_name' => $product->name, 'product_price' => $product->price,
                'product_image' => $product->image, 'quantity' => $quantity])->save();
        });
        return response()->json(['status' => 'success', 'message' => 'Added to cart']);
    }

    public function updateQty(Request $request)
    {
        $data = $request->validate(['cart_id' => 'required|integer', 'qty' => 'required|integer|min:1|max:1000000']);
        DB::transaction(function () use ($data) {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $cart = Cart::where('user_id', Auth::id())->findOrFail($data['cart_id']);
            $product = Product::lockForUpdate()->findOrFail($cart->product_id);
            $this->available($product, $data['qty']);
            $cart->update(['quantity' => $data['qty'], 'product_price' => $product->price]);
        });
        return response()->json(['status' => 'success']);
    }

    public function remove(Request $request)
    {
        $request->validate(['cart_id' => 'required|integer']);
        DB::transaction(function () use ($request) {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            Cart::where('user_id', Auth::id())->findOrFail($request->cart_id)->delete();
        });
        return response()->json(['status' => 'success']);
    }

    public function checkout()
    {
        $storeNames = DB::transaction(function () {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $items = Cart::where('user_id', Auth::id())->orderBy('product_id')->lockForUpdate()->get();
            if ($items->isEmpty()) throw ValidationException::withMessages(['cart' => 'Keranjang kosong.']);
            $groups = [];
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product) throw ValidationException::withMessages(['cart' => 'Produk sudah tidak tersedia.']);
                $this->available($product, $item->quantity);
                $groups[$product->store_id][] = [$product, $item->quantity];
            }
            $storeNames = [];
            foreach ($groups as $storeId => $products) {
                $storeNames[] = $products[0][0]->store->store_name;
                $totalCents = 0;
                foreach ($products as [$product, $quantity]) $totalCents += (int) round($product->price * 100) * $quantity;
                if ($totalCents > 999999999999) throw ValidationException::withMessages(['cart' => 'Nilai pesanan terlalu besar.']);
                $order = Order::create(['invoice_number' => 'INV-'.Str::uuid(), 'user_id' => Auth::id(),
                    'store_id' => $storeId, 'total_price' => $totalCents / 100, 'status' => 'Preparing',
                    'stock_reserved' => true]);
                foreach ($products as [$product, $quantity]) {
                    OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id,
                        'quantity' => $quantity, 'price_per_item' => $product->price]);
                    $product->stock -= $quantity;
                    $product->sold_count += $quantity;
                    if ($product->stock === 0) $product->status = 'out_of_stock';
                    $product->save();
                }
            }
            Cart::where('user_id', Auth::id())->delete();
            return $storeNames;
        });
        return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dikirim ke: '.implode(', ', $storeNames)]);
    }
}
