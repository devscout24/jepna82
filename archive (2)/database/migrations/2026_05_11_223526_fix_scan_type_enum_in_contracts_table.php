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
        // For MySQL, we can use a raw statement to update the ENUM
        DB::statement("ALTER TABLE contracts MODIFY COLUMN scan_type ENUM('free_preview', 'partial', 'basic', 'pro', 'full') DEFAULT 'partial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE contracts MODIFY COLUMN scan_type ENUM('free_preview', 'full') DEFAULT 'full'");
    }
};
