<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ticket_headers', 'update_by')) {
            Schema::table('ticket_headers', function (Blueprint $table) {
                $table->string('update_by',20)->nullable();
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
        if (Schema::hasColumn('ticket_headers', 'update_by')) {
            Schema::table('ticket_headers', function (Blueprint $table) {
                $table->dropColumn('update_by')->nullable();
            });
        }
    }
}
