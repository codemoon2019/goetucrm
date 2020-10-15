<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempOriginalResidualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_original_residuals', function (Blueprint $table) {
            $table->string('effective_date',100)->nullable();
            $table->string('file_number',100)->nullable();
            $table->string('name',200)->nullable();
            $table->string('merchant_name',200)->nullable();
            $table->string('rev_share',100)->nullable();
            $table->string('bankcard_vol',100)->nullable();
            $table->string('program_code',100)->nullable();
            $table->string('sales_rep',100)->nullable();
            $table->string('office',200)->nullable();
            $table->string('agent_id',100)->nullable();
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
        Schema::dropIfExists('temp_original_residuals');
    }
}
