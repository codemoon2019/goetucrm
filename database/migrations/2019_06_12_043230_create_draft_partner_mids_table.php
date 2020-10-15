<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftPartnerMidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draft_partner_mids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id')->nullable();
            $table->string('mid',50)->nullable();
            $table->integer('system_id')->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
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
        Schema::dropIfExists('draft_partner_mids');
    }
}
