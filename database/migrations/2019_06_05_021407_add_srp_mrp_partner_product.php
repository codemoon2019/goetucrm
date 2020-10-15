<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddSrpMrpPartnerProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partner_products', 'srp')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->decimal('srp',18,2)->default(0)->nullable();
            }); 
        }
        if (!Schema::hasColumn('partner_products', 'mrp')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->decimal('mrp',18,2)->default(0)->nullable();
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
        if (!Schema::hasColumn('partner_products', 'srp')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->dropColumn('srp');
            }); 
        }

        if (!Schema::hasColumn('partner_products', 'mrp')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->dropColumn('mrp');
            }); 
        }
    }
}
