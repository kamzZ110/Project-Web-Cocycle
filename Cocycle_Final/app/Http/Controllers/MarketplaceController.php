<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $current_category = $request->input('category', 'All');
        $search_keyword = $request->input('search', '');
        $resto_filter = $request->input('resto', '');
        
        $query = Product::with('store')->where('status', 'active')->where('stock', '>', 0)
            ->whereHas('store', fn ($q) => $q->where('status', 'active')->whereHas('user', fn ($owner) => $owner->where('role', 'partner')));

        // Filter Category
        if ($current_category !== 'All') {
            $query->where('category', $current_category);
        }

        // Filter Search
        if (!empty($search_keyword)) {
            $query->where(function ($q) use ($search_keyword) {
                $q->where('name', 'like', '%' . $search_keyword . '%')
                  ->orWhere('description', 'like', '%' . $search_keyword . '%');
            });
        }

        // Filter Resto
        if (!empty($resto_filter)) {
            $query->whereHas('store', function ($q) use ($resto_filter) {
                $q->where('store_name', $resto_filter);
            });
        }

        // Identical listings from different stores share a card, but remain
        // distinct products so the customer explicitly chooses the seller.
        $products = $query->orderBy('id')->get()->groupBy(fn ($product) => json_encode([
            $product->name, $product->category, (string) $product->price,
            $product->image, $product->description,
        ]));

        // Title logic
        $filter_title = "Explore Fresh Products";
        if ($current_category !== 'All') {
            $filter_title = $current_category;
        } elseif (!empty($search_keyword)) {
            $filter_title = 'Search: "' . $search_keyword . '"';
        } elseif (!empty($resto_filter)) {
            $filter_title = 'Store: ' . $resto_filter;
        }

        return view('marketplace.index', compact('products', 'current_category', 'search_keyword', 'filter_title'));
    }
}
