<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('partners', 'front_end_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('front_end_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('partners', 'back_end_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('back_end_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('partners', 'reporting_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('reporting_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('partners', 'pricing_type')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('pricing_type',150)->nullable();
            }); 
        }


        if (!Schema::hasColumn('draft_partners', 'front_end_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->string('front_end_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('draft_partners', 'back_end_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->string('back_end_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('draft_partners', 'reporting_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->string('reporting_mid',150)->nullable();
            }); 
        }

        if (!Schema::hasColumn('draft_partners', 'pricing_type')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->string('pricing_type',150)->nullable();
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

        if (Schema::hasColumn('partners', 'front_end_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('front_end_mid');
            }); 
        }

        if (Schema::hasColumn('partners', 'back_end_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('back_end_mid');
            }); 
        }

        if (Schema::hasColumn('partners', 'reporting_mid')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('reporting_mid');
            }); 
        }

        if (Schema::hasColumn('partners', 'pricing_type')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('pricing_type');
            }); 
        }

        if (Schema::hasColumn('draft_partners', 'front_end_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->dropColumn('front_end_mid');
            }); 
        }

        if (Schema::hasColumn('draft_partners', 'back_end_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->dropColumn('back_end_mid');
            }); 
        }

        if (Schema::hasColumn('draft_partners', 'reporting_mid')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->dropColumn('reporting_mid');
            }); 
        }

        if (Schema::hasColumn('draft_partners', 'pricing_type')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->dropColumn('pricing_type');
            }); 
        }
    }
}
