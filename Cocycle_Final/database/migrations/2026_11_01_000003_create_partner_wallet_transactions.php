<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partner_wallet_transactions')) {
            Schema::create('partner_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                // Income rows point to an order; withdrawal rows leave this null.
                $table->unsignedBigInteger('order_id')->nullable()->unique();
                $table->string('type', 20); // income | withdrawal
                $table->decimal('amount', 12, 2);
                $table->string('status', 20)->default('confirmed'); // confirmed | pending | cancelled
                $table->string('bank_name')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'type', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_wallet_transactions');
    }
};
