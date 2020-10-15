<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('description',200);
            $table->string('status',1);
            $table->string('create_by',50)->nullable();
            $table->integer('user_type_id')->nullable();
            $table->integer('sequence')->nullable();
            $table->integer('included_in_partners')->nullable();
            $table->string('upline',100)->nullable();
            $table->integer('included_in_agents')->nullable();
            $table->integer('included_in_leads')->nullable();
            $table->integer('included_in_training')->nullable();
            $table->string('initial',10)->nullable();
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
        Schema::dropIfExists('partner_types');
    }
}
