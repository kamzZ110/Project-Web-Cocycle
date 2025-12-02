<?php

use Illuminate\Support\Facades\Route;
// PENTING: Panggil Controller yang kamu pakai di sini
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AuthController; // Kalau nanti kamu buat AuthController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. ROUTE HOMEPAGE UTAMA ---
// Ini menggantikan halaman welcome bawaan Laravel.
// Sekarang kalau buka localhost:8000, dia akan memanggil fungsi index di DonationController
// Pastikan di controller nanti return view('home'); (file homepage_1.php kamu tadi)
Route::get('/', [DonationController::class, 'index'])->name('home');


// --- 2. ROUTE AUTHENTICATION (LOGIN & REGISTER) ---
// Ini supaya halaman login/signup kamu tetap bisa dibuka
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::get('/signup', function () {
    return view('auth.register'); 
})->name('register');

// Nanti kalau Logika Login sudah dipindah ke Controller, ubah jadi begini:
// Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
// Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');


// --- 3. ROUTE DONATION (FITUR UTAMA) ---
// Group ini diproteksi 'middleware auth', artinya HANYA user yang sudah login yang bisa akses.
// Kalau belum login coba akses ini, otomatis dilempar ke halaman login.
Route::prefix('donation')->middleware('auth')->group(function () {
    
    // Dashboard Donasi
    Route::get('/', [DonationController::class, 'index'])->name('donation.index');
    
    // Keranjang & Transaksi
    Route::get('/cart', [DonationController::class, 'cart'])->name('donation.cart');
    Route::post('/add-to-cart/{id}', [DonationController::class, 'addToCart'])->name('donation.add_cart');
    Route::patch('/update-cart', [DonationController::class, 'updateCart'])->name('donation.update_cart');
    Route::delete('/remove-from-cart', [DonationController::class, 'removeCart'])->name('donation.remove_cart');
    
    // Checkout Process
    Route::get('/checkout', [DonationController::class, 'checkout'])->name('donation.checkout');
    Route::post('/process', [DonationController::class, 'process'])->name('donation.process');
    
    // Halaman Lainnya
    Route::get('/community/{id}', [DonationController::class, 'communityDetail'])->name('donation.community');
    Route::get('/thank-you', [DonationController::class, 'thankYou'])->name('donation.thankyou');
    Route::get('/impact', [DonationController::class, 'impact'])->name('donation.impact');
});