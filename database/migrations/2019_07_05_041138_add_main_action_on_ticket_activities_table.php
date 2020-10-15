<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMainActionOnTicketActivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->string('main_action')->after('id');
        });
    }

    public function down()
    {
        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->dropColumn('main_action');
        });
    }
}
