<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code',10)->nullable();
            $table->string('description',100)->nullable();
            $table->text('remarks')->nullable();
            $table->integer('sequence')->nullable();
            $table->integer('query_sequence')->nullable();
            $table->string('create_by',20)->nullable();
            $table->char('status',1)->default('A')->nullable();
            $table->integer('is_admin')->default(0)->nullable();
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
        Schema::dropIfExists('ticket_filters');
    }
}
