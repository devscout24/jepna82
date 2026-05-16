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
        // 1. CONTRACTS Table
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('contract_title')->nullable();
            $table->string('document_type')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('file')->nullable();
            $table->longText('file_text')->nullable();
            $table->integer('file_size_kb')->nullable();
            $table->integer('page_count')->default(1);
            $table->enum('file_type', ['pdf', 'image', 'docx', 'txt'])->nullable();
            $table->enum('scan_type', ['free_preview', 'full'])->default('full');
            $table->enum('billing_mode', ['free', 'subscription', 'bulk', 'one_time', 'one_time_basic', 'one_time_pro'])->nullable();
            $table->decimal('amount_charged', 10, 2)->default(0);
            $table->integer('credit_used')->default(0);
            $table->decimal('per_page_rate', 10, 4)->nullable();
            $table->integer('extra_pages')->default(0);
            $table->decimal('extra_page_charge', 10, 2)->default(0);
            $table->bigInteger('payment_reference_id')->nullable();
            $table->bigInteger('subscription_id')->nullable();
            $table->bigInteger('credit_pack_id')->nullable();
            $table->dateTime('preview_generated_at')->nullable();
            $table->text('short_summary')->nullable();
            $table->integer('fairness_score')->nullable();
            $table->integer('risk_score')->nullable();
            $table->enum('risk_level', ['Low', 'Medium', 'High'])->nullable();
            $table->integer('risk_count')->default(0);
            $table->json('preview_risks')->nullable();
            $table->dateTime('full_unlocked_at')->nullable();
            $table->json('summary')->nullable();
            $table->json('risks')->nullable();
            $table->json('risk_overview')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('action_items')->nullable();
            $table->json('contract_details')->nullable();
            $table->json('key_clauses')->nullable();
            $table->json('fairness')->nullable();
            $table->json('signature_details')->nullable();
            $table->json('raw_ai_response')->nullable();
            $table->enum('access_level', ['preview', 'locked', 'unlocked'])->default('locked');
            $table->tinyInteger('is_full_unlocked')->default(0);
            $table->string('ai_model_used', 50)->nullable();
            $table->integer('token_count_preview')->default(0);
            $table->integer('token_count_full')->default(0);
            $table->dateTime('file_deleted_at')->nullable();
            $table->tinyInteger('is_file_deleted')->default(0);
            $table->enum('status', ['uploaded', 'preview_processing', 'preview_ready', 'full_processing', 'completed', 'failed'])->default('uploaded');
            $table->timestamps();
        });

        // 2. CONTRACT RISK ITEMS
        Schema::create('contract_risk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->enum('severity', ['Low', 'Medium', 'High'])->nullable();
            $table->enum('category', ['payment', 'termination', 'liability', 'renewal', 'fees', 'obligations', 'other'])->nullable();
            $table->text('explanation')->nullable();
            $table->text('impact')->nullable();
            $table->text('suggestion')->nullable();
            $table->tinyInteger('is_preview')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. RISK OVERVIEW
        Schema::create('risk_overviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->integer('total_risks')->default(0);
            $table->integer('high_risk_count')->default(0);
            $table->integer('medium_risk_count')->default(0);
            $table->integer('low_risk_count')->default(0);
            $table->timestamps();
        });

        // 4. CONTRACT KEY CLAUSES
        Schema::create('contract_key_clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->enum('clause_type', ['payment_terms', 'termination', 'renewal', 'liability', 'obligations', 'fees_penalties', 'other']);
            $table->text('clause_text')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 5. WALLET TRANSACTIONS
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('package_and_subscriptions');
            $table->foreignId('subscription_id')->nullable();
            $table->foreignId('credit_pack_id')->nullable();
            $table->foreignId('contract_id')->nullable()->constrained('contracts');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->enum('type', ['credit', 'debit', 'refund', 'adjustment']);
            $table->enum('source', [
                'subscription_purchase', 'subscription_renewal', 'bulk_purchase',
                'one_time_purchase', 'free_preview', 'admin_grant',
                'scan_usage', 'refund', 'expiry_deduction'
            ]);
            $table->integer('credits');
            $table->integer('previous_balance');
            $table->integer('current_balance');
            $table->integer('refundable_credit')->default(0);
            $table->enum('refund_status', ['not_refunded', 'partial', 'refunded'])->default('not_refunded');
            $table->string('stripe_payment_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('contract_key_clauses');
        Schema::dropIfExists('risk_overviews');
        Schema::dropIfExists('contract_risk_items');
        Schema::dropIfExists('contracts');
    }
};
