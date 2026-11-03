<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('stores', 'starter_catalog_initialized_at')) {
            Schema::table('stores', fn (Blueprint $table) => $table->timestamp('starter_catalog_initialized_at')->nullable());
        }
    }

    public function down(): void
    {
        // Preserve the marker: removing it could repopulate a deliberately emptied store.
    }
};
