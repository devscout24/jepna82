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
        Schema::create('package_and_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('sub_title')->nullable();
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');

            $table->integer('page_limit')->default(0);
            
            $table->decimal('extra_page_rate', 10, 4)->default(0.00)->comment('charge per extra page beyond page_limit');

            $table->json('features')->nullable()->comment('array of feature strings for pricing card');

            // Plan classification
            $table->enum('package_type', [
                'free_preview',
                'one_time_basic',
                'one_time_pro',
                'bulk',
                'subscription'
            ]);

            $table->enum('billing_cycle', [
                'one_time',
                'monthly',
                'yearly',
                'lifetime'
            ])->nullable();

            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_plan_id')->nullable();
            $table->integer('trial_days')->default(0);
            $table->tinyInteger('is_popular')->default(0);
            $table->string('badge_text', 50)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('package_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_and_subscriptions');
    }
};
