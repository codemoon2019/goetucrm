<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubTaskTemplateHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_task_template_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->nullable();
            $table->text('description')->nullable();
            $table->string('remarks',100)->nullable();
            $table->integer('days_to_complete')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->char('status',1)->default('A')->nullable();
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
        Schema::dropIfExists('sub_task_template_headers');
    }
}
