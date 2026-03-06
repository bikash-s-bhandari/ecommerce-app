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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('gateway', 30)->default('stripe'); // stripe, paypal
            $table->string('transaction_id', 200)->nullable()->unique(); // PaymentIntent
            $table->string('payment_method', 60)->nullable(); // pm_xxx
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->json('gateway_response')->nullable(); // raw Stripe response snapshot
            $table->string('idempotency_key', 100)->nullable()->unique(); // prevent duplicate payments
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
