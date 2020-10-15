<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ticket_details', 'is_internal')) {
            Schema::table('ticket_details', function (Blueprint $table) {
                $table->integer('is_internal')->default(0)->nullable();
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
        Schema::disableForeignKeyConstraints();
        if (!Schema::hasColumn('ticket_details', 'is_internal')) {
            Schema::table('ticket_details', function (Blueprint $table) {
                $table->dropColumn('is_internal');
            });
        }
    }
}
