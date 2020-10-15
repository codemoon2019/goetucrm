<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assigned_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->integer('partner_type_id')->nullable();
            $table->integer('previous_assigned_id')->nullable();
            $table->integer('creator_id')->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('status',1)->default('N');
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
        Schema::dropIfExists('incoming_leads');
    }
}
