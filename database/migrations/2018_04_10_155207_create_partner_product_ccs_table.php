<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerProductCcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_product_ccs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('product_id');
            $table->decimal('buy_rate',18,4);
            $table->string('payment_frequency',100);
            $table->integer('days_before_due_date');
            $table->text('due_date');
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->string('status',1);
            $table->integer('mark_up_type_id');
            $table->integer('price_rule_type_id');
            $table->decimal('mark_up_value',18,2);
            $table->decimal('price_value_min',18,2);
            $table->decimal('price_value_max',18,2);
            $table->string('split_type',100);
            $table->integer('is_split_percentage');
            $table->decimal('other_buy_rate',18,4);
            $table->decimal('downline_buy_rate',18,4);
            $table->decimal('upline_percentage',18,4);
            $table->decimal('downline_percentage',18,4); 
            $table->string('pricing_option',10); 
            $table->decimal('price',18,4);
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
        Schema::dropIfExists('partner_product_ccs');
    }
}
