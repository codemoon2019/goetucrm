<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('name',100);
            $table->integer('document_id');
            $table->text('document_image');
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->string('status',1);
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
        Schema::dropIfExists('partner_attachments');
    }
}
