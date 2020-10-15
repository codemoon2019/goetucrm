<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubTaskDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_task_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_task_id')->nullable();
            $table->integer('task_no')->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->string('assignee',500)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('prerequisite',10)->nullable();
            $table->char('status',1)->nullable();
            $table->string('link_condition',50)->nullable();
            $table->integer('link_number')->nullable();
            $table->string('product_tags',500)->nullable();
            $table->integer('days_to_complete')->nullable();
            $table->string('update_by',255)->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->integer('exported')->nullable();
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
        Schema::dropIfExists('sub_task_details');
    }
}
