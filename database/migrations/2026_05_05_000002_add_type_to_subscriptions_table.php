<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('subscriptions', 'type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('type')->default('default')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('subscriptions', 'type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
}
