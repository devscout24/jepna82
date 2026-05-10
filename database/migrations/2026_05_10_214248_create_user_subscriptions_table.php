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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();

    // Relations
    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('package_id')
        ->constrained('package_and_subscriptions')
        ->cascadeOnDelete();

    // Stripe
    $table->string('stripe_subscription_id')->nullable();
    $table->string('stripe_customer_id')->nullable();
    $table->string('stripe_invoice_id')->nullable();

    // Billing
    $table->decimal('amount', 10, 2);

    $table->string('currency', 10)
        ->default('USD');

    $table->dateTime('start_date');

    $table->dateTime('end_date')
        ->nullable();

    $table->dateTime('next_billing_date')
        ->nullable();

    $table->dateTime('trial_ends_at')
        ->nullable();

    // Credit Tracking
    $table->integer('credits_allocated')
        ->default(0);

    $table->integer('credits_used')
        ->default(0);

    $table->integer('credits_remaining')
        ->default(0);

    $table->dateTime('credits_reset_at')
        ->nullable();

    // Payment Status
    $table->enum('payment_status', [
        'pending',
        'paid',
        'failed',
        'cancelled'
    ])->default('pending');

    // Subscription Status
    $table->enum('subscription_status', [
        'trialing',
        'active',
        'past_due',
        'cancelled',
        'expired',
        'inactive'
    ])->default('inactive');

    // Cancellation
    $table->tinyInteger('cancel_at_period_end')
        ->default(0)
        ->comment('1 = will cancel at end of current period');

    $table->dateTime('cancelled_at')
        ->nullable();

    $table->text('cancellation_reason')
        ->nullable();

    $table->timestamps();

    // Indexes
    $table->index('user_id');

    $table->index('subscription_status');

    $table->index(
        ['user_id', 'subscription_status'],
        'idx_user_active_sub'
    );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
