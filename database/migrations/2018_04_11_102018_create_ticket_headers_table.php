<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject',100)->nullable();
            $table->char('status',10)->nullable();
            $table->char('type',10)->nullable();
            $table->char('priority',10)->nullable();
            $table->mediumText('department')->nullable();//tinyText('department'); => no tinytext
            $table->dateTime('ticket_date')->nullable();
            $table->dateTime('close_date')->nullable();
            $table->text('description')->nullable();
            $table->text('attachment')->nullable();
            $table->string('create_by',20)->nullable();
            $table->string('source_email',100)->nullable();
            $table->text('assignee')->nullable();
            $table->text('cc')->nullable();
            $table->text('tags')->nullable();
            $table->integer('parent_id')->default(-1)->nullable();
            $table->integer('requester_id')->default(-1)->nullable();
            $table->integer('is_internal')->default(0)->nullable();
            $table->text('email_message_id')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('ticket_headers');
    }
}
