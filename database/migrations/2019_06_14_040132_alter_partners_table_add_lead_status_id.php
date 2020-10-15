<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddLeadStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'lead_status_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->integer('lead_status_id')->default(1)->nullable();
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
        if (Schema::hasColumn('partners', 'lead_status_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('lead_status_id');
            }); 
        }

    }
}
