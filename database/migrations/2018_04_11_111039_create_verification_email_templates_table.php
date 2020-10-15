<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerificationEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_email_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('name',100);
            $table->text('description');
            $table->string('create_by',20);
            $table->string('update_by',20);
            $table->char('status',1);
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
        Schema::dropIfExists('verification_email_templates');
    }
}
