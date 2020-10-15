<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->increments('id');

            $table->text('changes');
            $table->timestamp('support_responsed_at')->nullable()->default(null);
            $table->timestamp('department_responsed_at')->nullable()->default(null);
            $table->timestamp('started_progress_at')->nullable()->default(null); 
            $table->timestamp('solved_at')->nullable()->default(null);

            $table->unsignedInteger('ticket_header_id');
            $table->foreign('ticket_header_id')
                ->references('id')
                ->on('ticket_headers');

            $table->string('create_by', 20)->nullable();
            $table->string('update_by', 20)->nullable();

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
        Schema::dropIfExists('ticket_activities');
    }
}
