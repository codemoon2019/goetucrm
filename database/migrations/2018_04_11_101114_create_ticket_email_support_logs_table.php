<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketEmailSupportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_email_support_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from',100)->nullable();
            $table->string('to',100)->nullable();
            $table->string('subject',100)->nullable();
            $table->text('message')->nullable();
            $table->integer('is_ticket')->default(0)->nullable();
            $table->string('create_by',20)->default('SYSTEM')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_email_support_logs');
    }
}
