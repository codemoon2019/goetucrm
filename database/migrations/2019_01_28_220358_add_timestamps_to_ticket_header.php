<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToTicketHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_headers', function (Blueprint $table) {
            $table->timestamp('responsed_at_department')->nullable()->default(null);
            $table->timestamp('responsed_at_assignee')->nullable()->default(null);
            $table->timestamp('first_replied_at')->nullable()->default(null);
            $table->timestamp('finished_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_headers', function (Blueprint $table) {
            $table->dropColumn('responsed_at_department');
            $table->dropColumn('responsed_at_assignee');
            $table->dropColumn('first_replied_at');
            $table->dropColumn('finished_at');
        });
    }
}
