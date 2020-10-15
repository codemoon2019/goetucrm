<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('reason_of_action')->nullable();
            $table->unsignedInteger('merchant_status_id')->nullable();
            $table->foreign('merchant_status_id')
                ->references('id')
                ->on('merchant_statuses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign('partners_merchant_status_id_foreign');
            $table->dropColumn('merchant_status_id');
            $table->dropColumn('reason_of_action');
        });
    }
}
