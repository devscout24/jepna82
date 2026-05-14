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
        Schema::create('subscribe_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_text')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('price_text')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribe_plans');
    }
};
