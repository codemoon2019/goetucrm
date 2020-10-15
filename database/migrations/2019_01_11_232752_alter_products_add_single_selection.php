<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsAddSingleSelection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('products', 'single_selection')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('single_selection')->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_categories', 'single_selection')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->integer('single_selection')->default(0)->nullable();
                $table->integer('is_required')->default(0)->nullable();
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

        if (Schema::hasColumn('products', 'single_selection')) {
            Schema::table('products', function (Blueprint $table) {
                  $table->dropColumn('single_selection');
            }); 
        }

        if (!Schema::hasColumn('product_categories', 'single_selection')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->dropColumn('single_selection');
                $table->dropColumn('is_required');
            }); 
        }

        
    }
}
