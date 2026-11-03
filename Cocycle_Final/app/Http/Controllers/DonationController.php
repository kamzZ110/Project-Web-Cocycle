<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodItem;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category', '');
        $search_keyword = $request->input('search', '');

        $query = FoodItem::where('status', 'available');

        // Filter Category (Bilingual Support: Indo/Eng)
        if (!empty($category)) {
            if ($category == 'Makanan') {
                // Not Sayur/Vegetable AND Not Buah/Fruit
                $query->where(function ($q) {
                    $q->where('tags', 'like', '%Makanan%')
                      ->orWhere(function ($subQ) {
                          $subQ->where('tags', 'not like', '%Sayur%')
                               ->where('tags', 'not like', '%Vegetable%')
                               ->where('tags', 'not like', '%Buah%')
                               ->where('tags', 'not like', '%Fruit%');
                      });
                });
            } elseif ($category == 'Sayur') {
                $query->where(function($q) {
                    $q->where('tags', 'like', '%Sayur%')
                      ->orWhere('tags', 'like', '%Vegetable%');
                });
            } elseif ($category == 'Buah') {
                $query->where(function($q) {
                    $q->where('tags', 'like', '%Buah%')
                      ->orWhere('tags', 'like', '%Fruit%');
                });
            }
        }

        // Search
        if (!empty($search_keyword)) {
            $query->where('name', 'like', '%' . $search_keyword . '%');
        }

        $items = $query->get();
        // Map alias manually if Model accessor fails or just to be safe
        // But FoodItem model accessor should work.
        
        $donation_items = $items;

        return view('donation.index', compact('donation_items', 'category', 'search_keyword'));
    }
}
