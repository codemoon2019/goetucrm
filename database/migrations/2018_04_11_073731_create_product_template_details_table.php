<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_template_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id');
            $table->integer('product_id')->nullable();
            $table->decimal('buy_rate',18,4)->nullable();
            $table->string('payment_frequency',100)->nullable();
            $table->integer('days_before_due_date')->nullable();
            $table->text('due_date')->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
            $table->char('status',1)->nullable();
            $table->integer('mark_up_type_id')->nullable();
            $table->integer('price_rule_type_id')->nullable();
            $table->decimal('mark_up_value',18,2)->nullable();
            $table->decimal('price_value_min',18,2)->nullable();
            $table->decimal('price_value_max',18,2)->nullable();
            $table->string('split_type',100)->nullable();
            $table->tinyInteger('is_split_percentage')->nullable();
            $table->decimal('other_buy_rate',18,4)->nullable();
            $table->decimal('downline_buy_rate',18,4)->nullable();
            $table->decimal('upline_percentage',18,4)->nullable();
            $table->decimal('downline_percentage',18,4)->nullable();
            $table->string('pricing_option',10)->nullable();
            $table->decimal('price',18,2)->nullable();
            $table->string('commission_type',50)->nullable();
            $table->decimal('commission_fixed',18,4)->nullable();
            $table->text('commission_based')->nullable();
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
        Schema::dropIfExists('product_template_details');
    }
}
