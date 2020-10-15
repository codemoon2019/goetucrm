<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoEmailerToPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'auto_emailer')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->integer('auto_emailer')->default(0)->nullable();
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
        if (Schema::hasColumn('email_on_queues', 'invoice_header_id')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('auto_emailer');
            });
        }
    }
}
