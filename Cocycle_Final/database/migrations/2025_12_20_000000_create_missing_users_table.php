<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('phone', 20);
                $table->string('password')->nullable();
                $table->string('profile_pic')->nullable();
                $table->enum('role', ['customer', 'partner'])->default('customer');
                $table->string('kode_otp', 10)->nullable();
                $table->dateTime('batas_waktu')->nullable();
                $table->string('address')->nullable();
                $table->string('fullname')->nullable();
                $table->string('nama_toko')->nullable();
                $table->decimal('balance', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally preserve imported accounts on rollback.
    }
};
