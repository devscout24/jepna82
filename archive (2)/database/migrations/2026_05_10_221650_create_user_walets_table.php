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
        Schema::create('user_walets', function (Blueprint $table) {
           $table->id();

    // User
    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    // Source References
    $table->foreignId('package_id')
        ->nullable()
        ->constrained('package_and_subscriptions')
        ->nullOnDelete();

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

    $table->foreignId('payment_id')
        ->nullable()
        ->constrained('payments')
        ->nullOnDelete();

    // Transaction Type
    $table->enum('type', [
        'credit',
        'debit',
        'refund',
        'adjustment'
    ]);

    // Transaction Source
    $table->enum('source', [
        'subscription_purchase',
        'subscription_renewal',
        'bulk_purchase',
        'one_time_purchase',
        'free_preview',
        'admin_grant',
        'scan_usage',
        'refund',
        'expiry_deduction'
    ]);

    // Credit Details
    $table->integer('credits')
        ->comment('always positive; type determines direction');

    $table->integer('previous_balance');

    $table->integer('current_balance');

    // Refund Tracking
    $table->integer('refundable_credit')
        ->default(0);

    $table->enum('refund_status', [
        'not_refunded',
        'partial',
        'refunded'
    ])->default('not_refunded');

    // Stripe
    $table->string('stripe_payment_id')
        ->nullable();

    // Notes
    $table->text('note')
        ->nullable();

     $table->timestamps();

    // Indexes
    $table->index('user_id');

    $table->index('type');

    $table->index('source');

    // $table->index('contract_id');

    $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_walets');
    }
};
