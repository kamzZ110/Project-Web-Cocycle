<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnerController;

Route::get('/', [HomeController::class, 'index'])->name('landing');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [App\Http\Controllers\PasswordController::class, 'request'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordController::class, 'form'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

// Customer Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/home', [HomeController::class, 'customerIndex'])->name('customer.home');
});

// Partner Routes
Route::middleware(['auth', 'role:partner'])->group(function () {
    Route::get('/partner/home', [PartnerController::class, 'index'])->name('partner.home');
    Route::get('/partner/withdrawal', [App\Http\Controllers\PartnerWithdrawalController::class, 'index'])->name('partner.withdrawal');
    Route::post('/partner/withdrawal', [App\Http\Controllers\PartnerWithdrawalController::class, 'store'])->name('partner.withdrawal.store');
    Route::post('/partner/withdrawal/{id}/cancel', [App\Http\Controllers\PartnerWithdrawalController::class, 'cancel'])->name('partner.withdrawal.cancel');
    
    // Partner Product Management Routes
    Route::get('/partner/products', [PartnerController::class, 'products'])->name('partner.products');
    Route::get('/partner/products/create', [PartnerController::class, 'createProduct'])->name('partner.products.create');
    Route::post('/partner/products/store', [PartnerController::class, 'storeProduct'])->name('partner.products.store');
    Route::get('/partner/products/{id}/edit', [PartnerController::class, 'editProduct'])->name('partner.products.edit');
    Route::post('/partner/products/{id}/update', [PartnerController::class, 'updateProduct'])->name('partner.products.update');
    Route::delete('/partner/products/{id}/delete', [PartnerController::class, 'destroyProduct'])->name('partner.products.delete');

    // Partner Orders
    Route::get('/partner/orders', [App\Http\Controllers\PartnerOrderController::class, 'index'])->name('partner.orders.index');
    Route::get('/partner/orders/{id}', [App\Http\Controllers\PartnerOrderController::class, 'show'])->name('partner.orders.show');
    Route::post('/partner/orders/update-status', [App\Http\Controllers\PartnerOrderController::class, 'updateStatus'])->name('partner.orders.updateStatus');

    // Partner Profile
    Route::get('/partner/profile', [App\Http\Controllers\PartnerProfileController::class, 'index'])->name('partner.profile.index');
    Route::post('/partner/profile', [App\Http\Controllers\PartnerProfileController::class, 'update'])->name('partner.profile.update');

    // Partner Analysis
    Route::get('/partner/analysis', [App\Http\Controllers\PartnerAnalysisController::class, 'index'])->name('partner.analysis.index');

    // Partner Chat
    Route::get('/partner/chat', [App\Http\Controllers\PartnerChatController::class, 'index'])->name('partner.chat.index');
    Route::get('/partner/chat/contacts', [App\Http\Controllers\PartnerChatController::class, 'getContacts'])->name('partner.chat.contacts');
    Route::get('/partner/chat/search', [App\Http\Controllers\PartnerChatController::class, 'search'])->name('partner.chat.search');
    Route::get('/partner/chat/messages', [App\Http\Controllers\PartnerChatController::class, 'getMessages'])->name('partner.chat.messages');
    Route::post('/partner/chat/send', [App\Http\Controllers\PartnerChatController::class, 'sendMessage'])->name('partner.chat.send');
});


// Profile Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/profile/data', [App\Http\Controllers\ProfileController::class, 'getData'])->name('profile.data');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePicture'])->name('profile.photo');

    // Marketplace Routes
    Route::get('/marketplace', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');

    // Cart Routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateQty'])->name('cart.update');
    Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');

    // Donation Routes
    Route::get('/donation', [App\Http\Controllers\DonationController::class, 'index'])->name('donation.index');
    Route::post('/donation/add', [App\Http\Controllers\DonationCartController::class, 'addToCart'])->name('donation.add');
    Route::get('/donation/cart', [App\Http\Controllers\DonationCartController::class, 'viewCart'])->name('donation.cart');
    Route::post('/donation/cart', [App\Http\Controllers\DonationCartController::class, 'updateCart'])->name('donation.update');
    Route::post('/donation/checkout', [App\Http\Controllers\DonationCartController::class, 'checkout'])->name('donation.checkout');
    Route::get('/donation/impact', [App\Http\Controllers\ImpactController::class, 'index'])->name('donation.impact');
    Route::get('/donation/levels', [App\Http\Controllers\ImpactController::class, 'levels'])->name('donation.levels');
});

// require base_path('routes/debug.php');

Route::middleware(['auth', 'role:customer'])->prefix('chat')->name('customer.chat.')->group(function () {
    Route::get('/', [App\Http\Controllers\PartnerChatController::class, 'index'])->name('index');
    Route::get('/contacts', [App\Http\Controllers\PartnerChatController::class, 'getContacts'])->name('contacts');
    Route::get('/search', [App\Http\Controllers\PartnerChatController::class, 'search'])->name('search');
    Route::get('/messages', [App\Http\Controllers\PartnerChatController::class, 'getMessages'])->name('messages');
    Route::post('/send', [App\Http\Controllers\PartnerChatController::class, 'sendMessage'])->name('send');
});

// The demo seeder is run explicitly, not through a public route.
