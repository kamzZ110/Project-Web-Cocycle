<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Communities
        Schema::create('communities', function (Blueprint $table) {
            $table->id(); // Ini default BigInt, tidak apa-apa utk tabel baru
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('focus_area');
            $table->text('address')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });

        // 2. Food Items
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->decimal('quantity_available', 8, 2);
            $table->string('unit'); // kg, pcs
            $table->decimal('handling_fee', 10, 2)->default(0);
            $table->json('tags')->nullable(); // Untuk label potensi olahan
            $table->enum('status', ['available', 'reserved', 'donated'])->default('available');
            $table->timestamps();
        });

        // 3. Donations
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            
            // PENTING: Karena tabel users yg kamu upload id-nya Integer (bukan BigInt)
            // Kita pakai integer() biasa, bukan foreignId()
            $table->integer('user_id')->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreignId('community_id')->constrained('communities');
            
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method');
            $table->enum('status', ['pending', 'paid', 'picked_up', 'received', 'processed'])->default('pending');
            $table->timestamps();
        });

        // 4. Donation Details
        Schema::create('donation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained('food_items');
            $table->decimal('qty', 8, 2);
            $table->decimal('subtotal_fee', 12, 2);
            $table->timestamps();
        });

        // 5. Impact Trackings
        Schema::create('impact_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->string('product_produced');
            $table->string('product_qty_result');
            $table->decimal('co2_saved_kg', 8, 2)->nullable();
            $table->decimal('waste_saved_kg', 8, 2);
            $table->text('community_note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('impact_trackings');
        Schema::dropIfExists('donation_details');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('food_items');
        Schema::dropIfExists('communities');
    }
};