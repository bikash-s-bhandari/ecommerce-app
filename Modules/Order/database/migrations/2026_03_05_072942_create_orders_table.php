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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 60)->default('Home'); // Home, Work, etc.
            $table->string('full_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('street_1', 200);
            $table->string('street_2', 200)->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('country', 100)->default('US');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->json('shipping_address'); // snapshot of address at order time
            $table->enum(
                'status',
                ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded']
            )->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'status', 'created_at']);//composite indexing gareko useful for search and filter queries.
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_title', 200); // snapshot
            $table->string('product_sku', 60)->nullable(); // snapshot
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2); // snapshot
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('addresses');
    }
};
