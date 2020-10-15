<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->string('field_name',100)->nullable();
            $table->string('field_type',20)->nullable();
            $table->string('options',5000)->default('')->nullable();
            $table->tinyInteger('is_required_invoice')->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
            $table->char('status',1)->default('')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_custom_fields');
    }
}
