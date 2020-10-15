<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionTemplateHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_template_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('name',200);
            $table->text('description');
            $table->string('create_by',80);
            $table->string('update_by',80);
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
        Schema::dropIfExists('commission_template_headers');
    }
}
