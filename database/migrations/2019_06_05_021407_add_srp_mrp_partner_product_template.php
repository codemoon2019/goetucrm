<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddSrpMrpPartnerProductTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_template_details', 'srp')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->decimal('srp',18,2)->default(0)->nullable();
            }); 
        }
        if (!Schema::hasColumn('product_template_details', 'mrp')) {
            Schema::table('product_template_details', function (Blueprint $table) {
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
        if (!Schema::hasColumn('product_template_details', 'srp')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('srp');
            }); 
        }

        if (!Schema::hasColumn('product_template_details', 'mrp')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('mrp');
            }); 
        }
    }
}
