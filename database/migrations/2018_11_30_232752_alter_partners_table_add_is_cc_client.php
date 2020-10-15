<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddIsCcClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'is_cc_client')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->integer('is_cc_client')->default(0)->nullable();
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
        if (Schema::hasColumn('partners', 'is_cc_client')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('is_cc_client');
            }); 
        }
    }
}
