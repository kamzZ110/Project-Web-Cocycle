<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index(\App\Services\PartnerWallet $wallet)
    {
        $user = $wallet->reconcile(Auth::user());
        if ($user->role !== 'partner') {
            return redirect()->route('customer.home');
        }

        $store = $user->store;
        $total_income = 0;
        $recent_orders = [];
        $best_products = [];
        $nama_toko = $user->store?->store_name ?? $user->nama_toko ?? 'Nama Toko Anda';

        if ($store) {
            $nama_toko = $store->store_name ?? $nama_toko;
            
            // Available wallet balance (completed order value less the legacy 10% app fee and withdrawals).
            $total_income = (float) $user->balance;

            // Recent Orders
            $recent_orders = $store->orders()->with('user')->latest()->take(5)->get();

            // Best Products
            $best_products = $store->products()->orderByDesc('sold_count')->take(3)->get();
        }

        return view('partner.home', compact('user', 'store', 'total_income', 'recent_orders', 'best_products', 'nama_toko'));
    }
    public function products()
    {
        $user = Auth::user();
        $store = $user->store;
        $products = $store ? $store->products()->latest()->get() : collect([]);
        $nama_toko = $store?->store_name ?? $user->nama_toko ?? 'Toko Anda';

        return view('partner.products.index', compact('products', 'nama_toko'));
    }

    public function createProduct()
    {
        $user = Auth::user();
        $nama_toko = $user->store?->store_name ?? $user->nama_toko ?? 'Toko Anda';
        return view('partner.products.create', compact('nama_toko'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'stock' => 'required|integer|min:0|max:1000000',
            'status' => 'nullable|in:active,inactive,out_of_stock',
            'description' => 'nullable|string|max:10000',
            'image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            // Create store if not exists (though logic should ensure it exists on register or first usage)
            $store = $user->store()->create([
                'store_name' => $user->nama_toko ?? 'My Store',
                // Add default address etc if needed
            ]);
        }

        $imagePath = 'assets/default_product.png';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('prod_', true) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photos/products'), $filename);
            $imagePath = 'photos/products/' . $filename;
        }

        $store->products()->create([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'image' => $imagePath,
        ]);

        return redirect()->route('partner.products')->with('success', 'Product created successfully');
    }

    public function editProduct($id)
    {
        $user = Auth::user();
        $product = $user->store->products()->findOrFail($id);
        $nama_toko = $user->store?->store_name ?? $user->nama_toko ?? 'Toko Anda';
        
        return view('partner.products.edit', compact('product', 'nama_toko'));
    }

    public function updateProduct(Request $request, $id)
    {
         $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'price' => 'required|numeric|min:0|max:99999999.99|decimal:0,2',
            'stock' => 'required|integer|min:0|max:1000000',
            'status' => 'nullable|in:active,inactive,out_of_stock',
            'description' => 'nullable|string|max:10000',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Auth::user()->store->products()->findOrFail($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('prod_', true) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photos/products'), $filename);
            $product->image = 'photos/products/' . $filename;
        }

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('partner.products')->with('success', 'Product updated successfully');
    }

    public function destroyProduct($id)
    {
        $product = Auth::user()->store->products()->findOrFail($id);
        if (\App\Models\OrderItem::where('product_id', $product->id)->exists()) {
            $product->update(['status' => 'inactive']);
            return redirect()->route('partner.products')->with('success', 'Produk dinonaktifkan agar riwayat pesanan tetap tersimpan.');
        }
        $product->delete();
        return redirect()->route('partner.products')->with('success', 'Product deleted successfully');
    }
}
