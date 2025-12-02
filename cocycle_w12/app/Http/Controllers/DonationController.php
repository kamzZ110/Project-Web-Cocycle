<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodItem;
use App\Models\Community;
use App\Models\Donation;
use App\Models\DonationDetail;
use App\Models\ImpactTracking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    // 1. Donation Exploration
    public function index() {
        $items = FoodItem::where('status', 'available')
                         ->where('quantity_available', '>', 0)
                         ->get();
        return view('donation.index', compact('items'));
    }

    // Fungsi Tambah ke Cart (Session)
    public function addToCart(Request $request, $id) {
        $item = FoodItem::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            return redirect()->back()->with('info', 'Item sudah ada di keranjang');
        }

        $cart[$id] = [
            "name" => $item->name,
            "quantity" => 1,
            "unit" => $item->unit,
            "price" => $item->handling_fee,
            "store" => $item->store_name,
            "img" => $item->image_url,
            "tags" => $item->tags ?? [],
            "max_qty" => $item->quantity_available
        ];

        session()->put('cart', $cart);
        return redirect()->route('donation.cart')->with('success', 'Item masuk keranjang!');
    }

    // 2. Donation Cart
    public function cart() {
        return view('donation.cart');
    }

    // Update Qty di Cart
    public function updateCart(Request $request) {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
    }

    // Hapus item Cart
    public function removeCart(Request $request) {
        if($request->id) {
            $cart = session()->get('cart');
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
    }

    // 3. Donation Checkout
    public function checkout() {
        if(!session('cart') || count(session('cart')) == 0) {
            return redirect()->route('donation.index');
        }
        
        $communities = Community::all();
        return view('donation.checkout', compact('communities'));
    }

    // PROCESS CHECKOUT
    public function process(Request $request) {
        $request->validate([
            'community_id' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session('cart');
        $totalAmount = 0;
        foreach($cart as $id => $details) {
            $totalAmount += $details['price'] * $details['quantity'];
        }
        
        $shippingCost = 10000; 
        $grandTotal = $totalAmount + $shippingCost;

        DB::transaction(function() use ($request, $cart, $grandTotal) {
            $donation = Donation::create([
                'user_id' => Auth::id() ?? 1,
                'community_id' => $request->community_id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_amount' => $grandTotal,
                'payment_method' => $request->payment_method,
                'status' => 'paid'
            ]);

            foreach($cart as $id => $details) {
                DonationDetail::create([
                    'donation_id' => $donation->id,
                    'food_item_id' => $id,
                    'qty' => $details['quantity'],
                    'subtotal_fee' => $details['price'] * $details['quantity']
                ]);

                $item = FoodItem::find($id);
                $item->quantity_available -= $details['quantity'];
                if($item->quantity_available <= 0) {
                    $item->status = 'donated';
                }
                $item->save();
            }
        });

        session()->forget('cart');
        return redirect()->route('donation.thankyou');
    }

    // 4. Detail Community
    public function communityDetail($id) {
        $comm = Community::find($id);
        return response()->json($comm);
    }

    // 5. Thank You
    public function thankYou() {
        return view('donation.thankyou');
    }

    // 6. Impact Tracking
    public function impact() {
        $userId = Auth::id() ?? 1;
        
        $impacts = ImpactTracking::whereHas('donation', function($q) use ($userId){
            $q->where('user_id', $userId);
        })->with('donation.community')->get();

        $stats = [
            'total_donation' => Donation::where('user_id', $userId)->count(),
            'total_waste_saved' => $impacts->sum('waste_saved_kg'),
            'total_co2_saved' => $impacts->sum('co2_saved_kg'),
            'communities_helped' => Donation::where('user_id', $userId)->distinct('community_id')->count()
        ];

        return view('donation.impact', compact('impacts', 'stats'));
    }
}