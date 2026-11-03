<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stores
        if (!Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->string('store_name', 100)->nullable();
            $table->string('operational_hours', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('logo_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            });
        }

        // Products
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('store_id')->nullable();
            $table->string('name', 100);
            $table->string('category', 50)->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->integer('views_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->string('distance', 50)->default('1 km');
            $table->text('description')->nullable();
            $table->string('image');
            $table->timestamps();
            });
        }

        // Orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('store_id');
            $table->integer('user_id');
            $table->string('invoice_number', 50);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['Preparing', 'Ready', 'Completed', 'Cancelled'])->default('Preparing');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Indexes
            $table->index(['store_id', 'status']);
            $table->index(['status', 'created_at']);
            });
        }

        // Order Items
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->decimal('price_per_item', 10, 2);
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        // Cart
        if (!Schema::hasTable('cart')) {
            Schema::create('cart', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('product_id');
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->string('product_image')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        // Communities
        if (!Schema::hasTable('communities')) {
            Schema::create('communities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('focus_area', 100);
            $table->text('address')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
            });
        }

        // Donations
        if (!Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('community_id');
            $table->string('invoice_number', 50);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 50);
            $table->enum('status', ['pending', 'paid', 'picked_up', 'received', 'processed'])->default('pending');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            });
        }

        // Food Items (For Donation?)
        if (!Schema::hasTable('food_items')) {
            Schema::create('food_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('store_name', 100);
            $table->string('name', 100);
            $table->string('image_url')->nullable();
            $table->decimal('quantity_available', 8, 2);
            $table->string('unit', 20);
            $table->decimal('handling_fee', 10, 2)->default(0.00);
            $table->longText('tags')->nullable();
            $table->enum('status', ['available', 'reserved', 'donated'])->default('available');
            $table->timestamps();
            });
        }

        // Donation Details
        if (!Schema::hasTable('donation_details')) {
            Schema::create('donation_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('donation_id');
            $table->integer('food_item_id');
            $table->decimal('qty', 8, 2);
            $table->decimal('subtotal_fee', 12, 2);
            $table->timestamps();

            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
            $table->foreign('food_item_id')->references('id')->on('food_items')->onDelete('cascade');
            });
        }

        // Impact Tracking
        if (!Schema::hasTable('impact_trackings')) {
            Schema::create('impact_trackings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('donation_id');
            $table->string('product_produced', 100);
            $table->string('product_qty_result', 50);
            $table->decimal('co2_saved_kg', 8, 2)->nullable();
            $table->decimal('waste_saved_kg', 8, 2);
            $table->text('community_note')->nullable();
            $table->timestamps();
            
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
