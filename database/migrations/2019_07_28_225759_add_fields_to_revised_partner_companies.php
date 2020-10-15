<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToRevisedPartnerCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_companies', function (Blueprint $table) {
            $table->string('extension_3',20)->nullable()->after('extension_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_companies', function (Blueprint $table) {
            $table->dropColumn('extension_3');
        });
    }
}
