<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftPartnerAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draft_partner_attachments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('document_name',100);
            $table->text('document_image');

            $table->unsignedInteger('draft_partner_id');
            $table->foreign('draft_partner_id')
                ->references('id')
                ->on('draft_partners')
                ->onDelete('cascade');

            $table->unsignedInteger('document_id');
            $table->foreign('document_id')
                ->references('id')
                ->on('documents');
                
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
        Schema::dropIfExists('draft_partner_attachments');
    }
}
