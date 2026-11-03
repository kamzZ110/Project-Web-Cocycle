<?php

namespace App\Http\Controllers;

use App\Models\{Donation, DonationDetail, ImpactTracking, Community, FoodItem, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Session};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DonationCartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:food_items,id']);
        $food = FoodItem::findOrFail($request->id);
        $cart = Session::get('donation_cart', []);
        $index = array_search($food->id, array_column($cart, 'id'));
        $qty = $index === false ? 1 : $cart[$index]['qty'] + 1;
        $this->check($food, $qty);
        $item = ['id' => $food->id, 'name' => $food->name, 'price' => $food->handling_fee,
            'image' => asset(\App\Support\AssetPath::local($food->image_url, 'assets/default_product.png')), 'qty' => $qty];
        if ($index === false) $cart[] = $item;
        else $cart[$index] = $item;
        Session::put('donation_cart', $cart);
        return redirect()->route('donation.index')->with('success', 'Item added to donation cart');
    }

    private function check(FoodItem $food, int $qty): void
    {
        if ($food->status !== 'available' || $qty < 1 || $qty > $food->quantity_available) {
            throw ValidationException::withMessages(['qty' => 'Item donasi tidak tersedia atau jumlah melebihi persediaan.']);
        }
    }

    public function viewCart()
    {
        $cart = Session::get('donation_cart', []);
        return view('donation.cart', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $request->validate(['qty' => 'required|array', 'qty.*' => 'required|integer|min:0|max:1000000']);
        $cart = Session::get('donation_cart', []);
        foreach ($cart as $key => &$item) {
            $item['qty'] = $request->input("qty.$key", $item['qty']);
        }
        unset($item);
        Session::put('donation_cart', array_values(array_filter($cart, fn ($i) => $i['qty'] > 0)));
        return redirect()->route('donation.cart');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'community' => 'required|in:Greenpeace Indonesia,Bank Sampah Induk',
            'qty' => 'nullable|array', 'qty.*' => 'required|integer|min:0|max:1000000',
            'payment_method' => 'nullable|in:E-Wallet,Transfer Bank',
        ]);
        $cart = Session::get('donation_cart', []);
        DB::transaction(function () use ($request, $cart) {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $items = [];
            $totalCents = 0;
            foreach ($cart as $key => $item) {
                $qty = (int) $request->input("qty.$key", $item['qty']);
                if ($qty === 0) continue;
                $food = FoodItem::lockForUpdate()->find($item['id']);
                if (!$food) throw ValidationException::withMessages(['cart' => 'Item donasi sudah tidak tersedia.']);
                $this->check($food, $qty);
                $subtotal = (int) round($food->handling_fee * 100) * $qty;
                $totalCents += $subtotal;
                $items[] = [$food, $qty, $subtotal];
            }
            if (!$items) throw ValidationException::withMessages(['cart' => 'Keranjang donasi kosong.']);
            if ($totalCents > 999999999999) throw ValidationException::withMessages(['cart' => 'Nilai donasi terlalu besar.']);
            $community = Community::firstOrCreate(['name' => $request->community], ['focus_area' => 'General']);
            // As in the campus prototype this records a simulated payment, with no gateway charge.
            $donation = Donation::create(['user_id' => Auth::id(), 'community_id' => $community->id,
                'invoice_number' => 'INV-DON-'.Str::uuid(), 'total_amount' => $totalCents / 100,
                'payment_method' => $request->input('payment_method', 'E-Wallet'), 'status' => 'paid']);
            $totalQty = 0;
            foreach ($items as [$food, $qty, $subtotal]) {
                DonationDetail::create(['donation_id' => $donation->id, 'food_item_id' => $food->id,
                    'qty' => $qty, 'subtotal_fee' => $subtotal / 100]);
                $food->quantity_available -= $qty;
                if ($food->quantity_available <= 0) $food->status = 'donated';
                $food->save();
                $totalQty += $qty;
            }
            ImpactTracking::create(['donation_id' => $donation->id, 'product_produced' => 'Kompos / Pakan Ternak',
                'product_qty_result' => 'Setara '.($totalQty * 0.4).' kg',
                'co2_saved_kg' => $totalQty * 0.2, 'waste_saved_kg' => $totalQty * 0.5]);
        });
        Session::forget('donation_cart');
        return redirect()->route('donation.impact')->with('success', 'Donasi berhasil! Terima kasih atas kontribusi Anda.');
    }
}
