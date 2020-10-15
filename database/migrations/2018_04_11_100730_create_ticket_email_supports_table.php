<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketEmailSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_email_supports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company',100)->nullable();
            $table->string('server',100)->nullable();
            $table->string('email_address',100)->nullable();
            $table->string('password',100)->nullable();
            $table->string('port',20)->nullable();
            $table->char('status',1)->default('A')->nullable();
            $table->string('create_by',20)->nullable();
            $table->string('update_by',20)->nullable();
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
        Schema::dropIfExists('ticket_email_supports');
    }
}
