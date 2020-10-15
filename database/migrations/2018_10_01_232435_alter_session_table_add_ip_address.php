<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSessionTableAddIpAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sessions', 'ip_address')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->text('ip_address')->nullable();
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
        if (Schema::hasColumn('sessions', 'ip_address')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropColumn('ip_address');
            });
        }
    }
}
