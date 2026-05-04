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
        Schema::create('monthly_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('title')->nullable();
            $table->string('title_text')->nullable();
            $table->string('duration')->nullable();//monthly subscription duration
            $table->string('stripe_price_id'); // Stripe price ID
            $table->string('plan_id')->nullable(); // Optional plan ID for internal use
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_subscriptions');
    }
};
