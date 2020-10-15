<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOrderCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_order_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->text('comment')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('create_by',50)->nullable();
            $table->char('status',1)->default('A')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('is_public')->default(1)->nullable();
            $table->text('attachment')->nullable();
            $table->tinyInteger('is_internal')->default(0)->nullable();
            $table->string('comment_status',50)->nullable();
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
        Schema::dropIfExists('product_order_comments');
    }
}
