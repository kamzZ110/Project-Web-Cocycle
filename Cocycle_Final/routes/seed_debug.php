<?php

use Illuminate\Support\Facades\Route;
use Database\Seeders\PartnerSeeder;

Route::get('/debug-seed', function () {
    try {
        $seeder = new PartnerSeeder();
        $seeder->run();
        return "Seeding Successful!";
    } catch (\Exception $e) {
        return "Seeding Failed: " . $e->getMessage();
    }
});
