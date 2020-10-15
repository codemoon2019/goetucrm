<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubTaskTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_task_template_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_task_id')->nullable();
            $table->integer('line_number')->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->text('assignee')->nullable();
            $table->integer('days_to_complete')->nullable();
            $table->string('prerequisite',10)->nullable();
            $table->text('product_tags')->nullable();
            $table->string('link_condition',50)->nullable();
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
        Schema::dropIfExists('sub_task_template_details');
    }
}
