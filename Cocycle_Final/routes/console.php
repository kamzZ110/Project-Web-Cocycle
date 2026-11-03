<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cocycle:starter-products', function () {
    \App\Models\User::where('role', 'partner')->orderBy('id')->each(function ($user) {
        $store = app(\App\Services\PartnerStarterCatalog::class)->initialize($user);
        $this->line('Store '.$store->id.': '.$store->products()->count().' products');
    });
})->purpose('Initialize starter products once for empty partner stores; preserve existing catalogs and orders');
