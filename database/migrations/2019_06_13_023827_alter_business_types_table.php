<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBusinessTypesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('business_types');
        Schema::create('business_types', function (Blueprint $table) {
            $table->unsignedSmallInteger('mcc');
            $table->string('description', 255)->nullable();
            $table->longText('remarks')->nullable();
            $table->string('group');
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('status',1)->nullable();
            $table->timestamps();

            $table->primary('mcc');
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_types');
    }
}
