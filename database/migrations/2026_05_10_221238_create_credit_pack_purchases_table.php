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
        Schema::create('credit_pack_purchases', function (Blueprint $table) {
             $table->id();

    // Relations
    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('package_id')
        ->constrained('package_and_subscriptions')
        ->cascadeOnDelete();

    // $table->foreignId('payment_id')
    //     ->nullable()
    //     ->constrained('payments')
    //     ->nullOnDelete();

    // Credits
    $table->integer('credits_purchased');

    $table->integer('credits_used')
        ->default(0);

    $table->integer('credits_remaining');

    // Expiry
    $table->dateTime('expires_at')
        ->nullable()
        ->comment('null = never expires');

    // Status
    $table->enum('status', [
        'active',
        'exhausted',
        'expired',
        'refunded'
    ])->default('active');

    // Stripe
    $table->string('stripe_payment_intent_id')
        ->nullable();

    $table->timestamps();

    // Indexes
    $table->index('user_id');

    $table->index(
        ['user_id', 'status'],
        'idx_user_active_packs'
    );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_pack_purchases');
    }
};
