<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardResidualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_card_residuals', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('effective_date');
            $table->string('file_number',20);
            $table->string('name',200);
            $table->string('merchant_name',200);
            $table->decimal('rev_share',18,2);
            $table->decimal('bankcard_vol',18,2);
            $table->decimal('program_code',18,2);
            $table->string('sales_rep',100);
            $table->string('office',200);
            $table->string('agent_id',100);
            $table->string('create_by',20);
            $table->string('update_by',20);
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
        Schema::dropIfExists('credit_card_residuals');
    }
}
