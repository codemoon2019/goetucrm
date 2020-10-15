<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'company_id')) {
            Schema::table('partners', function (Blueprint $table) {
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
        if (Schema::hasColumn('partners', 'company_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
}
