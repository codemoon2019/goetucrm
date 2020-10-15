<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPartners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('draft_partners', function (Blueprint $table) {
            $table->string('partner_id_reference',80)->nullable();
            $table->integer('is_verified_email')->default(0)->nullable();

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
            $table->dropColumn('partner_id_reference');
            $table->dropColumn('is_verified_email');

        });
    }
}
