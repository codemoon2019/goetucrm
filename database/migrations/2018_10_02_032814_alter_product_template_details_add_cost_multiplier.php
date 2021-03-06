<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductTemplateDetailsAddCostMultiplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_template_details', 'cost_multiplier')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->integer('cost_multiplier')->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'cost_multiplier_value')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->decimal('cost_multiplier_value',18,2)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'cost_multiplier_type')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->string('cost_multiplier_type')->nullable();
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
        if (!Schema::hasColumn('product_template_details', 'cost_multiplier')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('cost_multiplier');
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'cost_multiplier_value')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('cost_multiplier_value');
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'cost_multiplier_type')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('cost_multiplier_type');
            }); 
        }

    }
}
