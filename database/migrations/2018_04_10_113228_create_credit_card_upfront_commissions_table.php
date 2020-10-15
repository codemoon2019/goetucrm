<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardUpfrontCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_card_upfront_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('effective_date');
            $table->string('parent_agent_id',100);
            $table->string('parent_agent_name',100);
            $table->string('file_number',20);
            $table->string('fdms_account_no',100);
            $table->string('dba_name',100);
            $table->string('first_post_date',100);
            $table->string('open_date',100);
            $table->string('closed_date',100);
            $table->string('equipment_model',100);
            $table->decimal('production_value',18,2);
            $table->decimal('bonus',18,2);
            $table->string('comment',100);
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
        Schema::dropIfExists('credit_card_upfront_commissions');
    }
}
