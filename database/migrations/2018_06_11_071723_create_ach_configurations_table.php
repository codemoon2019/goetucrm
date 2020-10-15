<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ach_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('sftp_address',255)->nullable();
            $table->string('sftp_user',255)->nullable();
            $table->string('sftp_password',255)->nullable();
            $table->string('pay_to',255)->nullable();
            $table->string('pay_token',255)->nullable();
            $table->string('create_by',20)->nullable();
            $table->string('update_by',20)->nullable();
            $table->char('status',1)->default('A');
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
        Schema::dropIfExists('ach_configurations');
    }
}
