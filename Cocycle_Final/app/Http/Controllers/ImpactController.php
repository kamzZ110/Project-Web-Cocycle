<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Donation;

class ImpactController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        // 1. Calculate Points (1 point per 1000 IDR)
        $total_rupiah = Donation::where('user_id', $user_id)
            ->where('status', 'paid')
            ->sum('total_amount');
            
        $my_points = floor($total_rupiah / 1000);

        // Level Logic
        $level = $this->calculateLevel($my_points);

        // 2. Chart Data (Monthly donations this year)
        $current_year = date('Y');
        $donations_monthly = Donation::where('user_id', $user_id)
            ->where('status', 'paid')->whereYear('created_at', $current_year)->get()
            ->groupBy(fn ($donation) => $donation->created_at->month)
            ->map(fn ($donations) => $donations->sum('total_amount'))->toArray();

        $chart_data = array_fill(1, 12, 0);
        foreach ($donations_monthly as $month => $total) {
            $chart_data[$month] = (int)$total;
        }
        $js_chart_data = json_encode(array_values($chart_data));

        // 3. Donation History
        $history = Donation::with('community')
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 4. Impact Metrics
        // Join donations -> impact_trackings
        $metrics = DB::table('impact_trackings')
            ->join('donations', 'impact_trackings.donation_id', '=', 'donations.id')
            ->where('donations.user_id', $user_id)
            ->where('donations.status', 'paid')
            ->select(
                DB::raw('SUM(co2_saved_kg) as total_co2'),
                DB::raw('SUM(waste_saved_kg) as total_waste')
            )
            ->first();

        $total_co2 = $metrics->total_co2 ?? 0;
        $total_waste = $metrics->total_waste ?? 0;

        return view('donation.impact', compact(
            'my_points', 
            'level', 
            'js_chart_data', 
            'current_year', 
            'history', 
            'total_co2', 
            'total_waste'
        ));
    }

    public function levels()
    {
        return view('donation.levels');
    }

    private function calculateLevel($points)
    {
        if ($points >= 7500) {
            return ['name' => 'Pemimpin Gerakan', 'color' => 'text-danger', 'border' => 'border-danger', 'icon' => 'fa-crown'];
        } elseif ($points >= 5000) {
            return ['name' => 'Penolong Lingkungan', 'color' => 'text-danger', 'border' => 'border-danger', 'icon' => 'fa-heart'];
        } elseif ($points >= 3000) {
            return ['name' => 'Pemberi Aktif', 'color' => 'text-warning', 'border' => 'border-warning', 'icon' => 'fa-hand-holding-heart'];
        } elseif ($points >= 500) {
            return ['name' => 'Kebaikan Baru', 'color' => 'text-primary', 'border' => 'border-primary', 'icon' => 'fa-star'];
        }
        return ['name' => 'Pemula', 'color' => 'text-muted', 'border' => 'border-secondary', 'icon' => 'fa-seedling'];
    }
}
