<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempUpfrontCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_upfront_commissions', function (Blueprint $table) {
            $table->string('effective_date',100)->nullable();
            $table->string('parent_agent_id',100)->nullable();
            $table->string('parent_agent_name',100)->nullable();
            $table->string('file_number',100)->nullable();
            $table->string('fdms_accout_no',100)->nullable();
            $table->string('dba_name',100)->nullable();
            $table->string('first_post_date',100)->nullable();
            $table->string('open_date',100)->nullable();
            $table->string('closed_date',100)->nullable();
            $table->string('equipment_model',100)->nullable();
            $table->string('production_value',100)->nullable();
            $table->string('bonus',100)->nullable();
            $table->string('comment',100)->nullable();
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
        Schema::dropIfExists('temp_upfront_commissions');
    }
}
