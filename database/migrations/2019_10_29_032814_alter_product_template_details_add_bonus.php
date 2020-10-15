<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductTemplateDetailsAddBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_template_details', 'bonus')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->integer('bonus')->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'bonus_amount')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->decimal('bonus_amount',18,2)->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'bonus_type')) {
            Schema::table('product_template_details', function (Blueprint $table) {
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
        if (!Schema::hasColumn('product_template_details', 'bonus')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('bonus');
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'bonus_amount')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('bonus_amount');
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'bonus_type')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('bonus_type');
            }); 
        }

    }

}
