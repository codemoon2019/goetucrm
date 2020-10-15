<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('description',200);
            $table->string('status',1);
            $table->string('create_by',50);
            $table->string('update_by',50);
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
        Schema::dropIfExists('partner_statuses');
    }
}
