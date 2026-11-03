<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'stock_reserved')) {
            Schema::table('orders', fn (Blueprint $table) => $table->boolean('stock_reserved')->default(false));
        }
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                // Indexed logical references accommodate legacy signed INT and new BIGINT users.
                $table->bigInteger('sender_id')->index();
                $table->bigInteger('receiver_id')->index();
                $table->text('message');
                $table->timestamps();
                $table->index(['sender_id', 'receiver_id', 'id']);
            });
        }
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Do not destroy existing messages or reset records on rollback.
    }
};
