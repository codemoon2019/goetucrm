<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTicketIdOnEmailOnQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_on_queues', function (Blueprint $table) {
            $table->integer('ticket_header_id')->default(-1);
            $table->integer('ticket_detail_id')->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_on_queues', function (Blueprint $table) {
            $table->dropColumn('ticket_header_id');
            $table->dropColumn('ticket_detail_id');
        });
    }
}
