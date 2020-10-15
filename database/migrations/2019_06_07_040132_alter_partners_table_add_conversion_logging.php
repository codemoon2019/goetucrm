<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddConversionLogging extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'original_partner_id_reference')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('original_partner_id_reference')->nullable();
            }); 
        }

        if (!Schema::hasColumn('partners', 'original_partner_type_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->integer('original_partner_type_id')->nullable();
            }); 
        }

        if (!Schema::hasColumn('partners', 'conversion_date')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dateTime('conversion_date')->nullable();
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
        if (Schema::hasColumn('partners', 'original_partner_id_reference')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('original_partner_id_reference');
            }); 
        }

        if (Schema::hasColumn('partners', 'original_partner_type_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('original_partner_type_id');
            }); 
        }

        if (Schema::hasColumn('partners', 'conversion_date')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('conversion_date');
            }); 
        }

    }
}
