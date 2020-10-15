<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToRevisedDrafts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('draft_partners', function (Blueprint $table) {
            $table->string('bank_address',80)->nullable()->after('bank_routing_no');
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
        Schema::table('draft_partners', function (Blueprint $table) {
            $table->dropColumn('bank_address');
            $table->dropColumn('extension_3');
        });
    }
}
