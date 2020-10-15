<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnerContactsAlterPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('partner_contacts', 'ownership_percentage')) {
            Schema::table('partner_contacts', function (Blueprint $table) {
                $table->string('ownership_percentage',50)->change();
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
        if (Schema::hasColumn('partner_contacts', 'ownership_percentage')) {
            Schema::table('partner_contacts', function (Blueprint $table) {
                $table->string('ownership_percentage',3)->change();
            }); 
        } 
    }
}
