<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnerProductsAddBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partner_products', 'bonus')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->integer('bonus')->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('partner_products', 'bonus_amount')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->decimal('bonus_amount',18,2)->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('partner_products', 'bonus_type')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->string('bonus_type')->default('percentage')->nullable();
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
        if (!Schema::hasColumn('partner_products', 'bonus')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->dropColumn('bonus');
            }); 
        }

        if (!Schema::hasColumn('partner_products', 'bonus_amount')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->dropColumn('bonus_amount');
            }); 
        }

        if (!Schema::hasColumn('partner_products', 'bonus_type')) {
            Schema::table('partner_products', function (Blueprint $table) {
                $table->dropColumn('bonus_type');
            }); 
        }

    }
}
