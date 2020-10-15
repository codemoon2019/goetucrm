<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceIdColumnOnEmailQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('email_on_queues', 'invoice_header_id')) {
            Schema::table('email_on_queues', function (Blueprint $table) {
                $table->integer('invoice_header_id')->default(-1)->nullable();
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
            Schema::table('email_on_queues', function (Blueprint $table) {
                $table->dropColumn('invoice_header_id');
            });
        }
    }
}
