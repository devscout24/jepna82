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

    // Relations
    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('package_id')
        ->constrained('package_and_subscriptions')
        ->cascadeOnDelete();

    // Purchased Item References
    $table->foreignId('subscription_id')
        ->nullable()
        ->constrained('user_subscriptions')
        ->nullOnDelete();

    $table->foreignId('credit_pack_id')
        ->nullable()
        ->constrained('credit_pack_purchases')
        ->nullOnDelete();

    // $table->foreignId('contract_id')
    //     ->nullable()
    //     ->constrained('contracts')
    //     ->nullOnDelete();

    // Payment Info
    $table->decimal('amount', 10, 2);

    $table->string('currency', 10)
        ->default('USD');

    $table->integer('credits_purchased')
        ->default(0);

    // Payment Gateway
    $table->enum('payment_method', [
        'stripe',
        'paypal',
        'bkash',
        'nagad'
    ]);

    // Stripe Fields
    $table->string('stripe_payment_intent_id')
        ->nullable();

    $table->string('stripe_charge_id')
        ->nullable();

    $table->string('stripe_customer_id')
        ->nullable();

    $table->string('stripe_invoice_id')
        ->nullable();

    $table->string('stripe_subscription_id')
        ->nullable();

    // Other Gateway Transaction ID
    $table->string('transaction_id')
        ->nullable();

    // Payment Status
    $table->enum('status', [
        'pending',
        'processing',
        'success',
        'failed',
        'refunded',
        'partially_refunded'
    ])->default('pending');

    // Payment Type
    $table->enum('payment_type', [
        'subscription',
        'one_time',
        'bulk',
        'extra_pages'
    ]);

    // Refund
    $table->decimal('refund_amount', 10, 2)
        ->default(0);

    $table->dateTime('refunded_at')
        ->nullable();

    $table->text('refund_reason')
        ->nullable();

    // Extra Page Charges
    $table->integer('extra_pages_charged')
        ->default(0);

    $table->decimal('extra_page_total', 10, 2)
        ->default(0);

    // Raw Gateway Response
    $table->json('raw_response')
        ->nullable();

    $table->timestamps();

    // Indexes
    $table->index('user_id');

    $table->index('status');

    $table->index('payment_type');

    $table->index('stripe_payment_intent_id');
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
