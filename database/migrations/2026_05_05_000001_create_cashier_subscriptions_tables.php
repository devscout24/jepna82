<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashierSubscriptionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('stripe_id')->index();
            $table->string('stripe_status')->nullable();
            $table->string('stripe_price')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->string('stripe_id')->index();
            $table->string('stripe_product')->nullable();
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
    }
}
