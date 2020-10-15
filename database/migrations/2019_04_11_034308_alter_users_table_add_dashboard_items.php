<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableAddDashboardItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'dashboard_items')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('dashboard_items')->nullable();
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
        if (!Schema::hasColumn('users', 'dashboard_items')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dashboard_items');
            }); 
        }
    }
}
