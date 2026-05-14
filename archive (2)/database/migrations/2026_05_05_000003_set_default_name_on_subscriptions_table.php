<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetDefaultNameOnSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('subscriptions')) {
            // Ensure existing null/empty names are set
            DB::statement("UPDATE `subscriptions` SET `name` = 'default' WHERE `name` IS NULL OR `name` = ''");

            // Alter column to set NOT NULL with default. Using raw SQL avoids requiring doctrine/dbal.
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `subscriptions` MODIFY `name` VARCHAR(255) NOT NULL DEFAULT 'default'");
            } elseif ($driver === 'sqlite') {
                // SQLite: recreate table would be required; skip altering and just update rows
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('subscriptions')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `subscriptions` MODIFY `name` VARCHAR(255) NULL DEFAULT NULL");
            }
        }
    }
}
