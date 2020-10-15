<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnerMailingAddressColumnAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (Schema::hasColumn('partner_mailing_addresses', 'address')) {
            Schema::table('partner_mailing_addresses', function (Blueprint $table) {
                $table->text('address')->nullable()->change();
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
        if (Schema::hasColumn('partner_mailing_addresses', 'address')) {
            Schema::table('partner_mailing_addresses', function (Blueprint $table) {
                $table->text('address')->change();
            }); 
        }
    }
}
