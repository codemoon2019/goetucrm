<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->string('charge_to',20)->nullable();
            $table->string('billing_cycle',20)->nullable();
            $table->integer('quantity')->nullable();
            $table->double('amount',18,2)->nullable();
            $table->timestamp('due_date')->nullable();
            $table->string('charge_type',20)->nullable();
            $table->string('payment_method',20)->nullable();
            $table->smallInteger('is_generate_voice')->nullable();
            $table->smallInteger('is_send_confirm_to_parent')->nullable();
            $table->smallInteger('is_send_confirm_to_child')->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
            $table->string('status',40)->nullable();
            $table->longText('signature')->nullable();
            $table->string('product_status',50)->nullable();
            $table->text('order_settings')->nullable();
            $table->string('sign_code',255)->nullable();
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
        Schema::dropIfExists('product_orders');
    }
}
