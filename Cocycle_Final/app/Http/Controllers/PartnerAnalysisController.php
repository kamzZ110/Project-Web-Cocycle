<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class PartnerAnalysisController extends Controller
{
    private function getStore()
    {
        return Store::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $store = $this->getStore();
        if (!$store) abort(404, 'Store not found');

        $user = Auth::user(); // For header display

        // Fetch Completed Orders (Transactions)
        $orders = Order::where('store_id', $store->id)
            ->where('status', '!=', 'Cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $total_gross = 0;
        $total_sold = 0; // Count of completed orders in Legacy logic

        foreach ($orders as $order) {
            if ($order->status == 'Completed') {
                $total_gross += $order->total_price;
                $total_sold++;
            }
        }

        // Match the 10% application fee shown on the legacy order detail page.
        $commission = $total_gross * 0.10;
        $net_income = $total_gross - $commission;

        return view('partner.analysis.index', compact('store', 'user', 'total_gross', 'total_sold', 'commission', 'net_income'));
    }
}
