<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnerContactsAddSsnVerified extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasColumn('partner_contacts', 'ssn_verified')) {
            Schema::table('partner_contacts', function (Blueprint $table) {
                $table->integer('ssn_verified')->default(0)->nullable();
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
        if (Schema::hasColumn('partner_contacts', 'ssn_verified')) {
            Schema::table('partner_contacts', function (Blueprint $table) {
                $table->dropColumn('ssn_verified');
            });
        }
    }
}
