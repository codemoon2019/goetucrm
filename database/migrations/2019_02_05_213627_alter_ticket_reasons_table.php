<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_reasons', function (Blueprint $table) {
            $table->unsignedInteger('ticket_priority_id')->nullable()->default(null);
            $table->foreign('ticket_priority_id')
                ->references('id')
                ->on('ticket_priorities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('ticket_reasons', function (Blueprint $table) {
            $table->dropForeign('ticket_reasons_ticket_priority_id_foreign');
            $table->dropColumn('ticket_priority_id');
        });
    }
}
