<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expanding the status enum to include partial and pro processing states
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status ENUM('uploaded', 'partial_processing', 'basic_processing', 'pro_processing', 'full_processing', 'preview_ready', 'completed', 'failed') DEFAULT 'uploaded'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status ENUM('uploaded', 'preview_processing', 'preview_ready', 'full_processing', 'completed', 'failed') DEFAULT 'uploaded'");
    }
};
