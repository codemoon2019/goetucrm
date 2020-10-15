<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDivisionAddPointPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('divisions', 'user_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->integer('user_id')->default(-1)->nullable();
            }); 
        }

        if (!Schema::hasColumn('divisions', 'company_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->integer('company_id')->default(-1)->nullable();
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
        if (Schema::hasColumn('divisions', 'user_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('divisions', 'company_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

    }
}
