<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function customerIndex()
    {
        // Ensure user is customer? Middleware handles auth, but role check is good.
        if (Auth::user()->role !== 'customer') {
            return redirect()->route('partner.home');
        }
        return view('customer.home');
    }
}
