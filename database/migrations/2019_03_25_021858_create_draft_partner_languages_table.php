<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftPartnerLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draft_partner_languages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('draft_partner_id');
            $table->foreign('draft_partner_id')
                ->references('id')
                ->on('draft_partners')
                ->onDelete('cascade');

            $table->unsignedInteger('draft_language_id');
            $table->foreign('draft_language_id')
                ->references('id')
                ->on('languages');
                
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
        Schema::dropIfExists('draft_partner_languages');
    }
}
