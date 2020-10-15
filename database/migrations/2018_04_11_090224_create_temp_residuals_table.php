<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempResidualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_residuals', function (Blueprint $table) {
            $table->string('effective_date',100)->nullable();
            $table->string('parent_agent_id',50)->nullable();
            $table->string('agent_id',50)->nullable();
            $table->string('sales_person_first_name',100)->nullable();
            $table->string('sales_person_last_name',100)->nullable();
            $table->string('fdms_number',100)->nullable();
            $table->string('file_number',100)->nullable();
            $table->string('dba_name',500)->nullable();
            $table->string('revenue_share_type',100)->nullable();
            $table->string('mc_sales_amount',100)->nullable();
            $table->string('mc_sales_return_amount',100)->nullable();
            $table->string('visa_sales_amount',100)->nullable();
            $table->string('visa_sales_return_amount',100)->nullable();
            $table->string('disc_sales_amount',100)->nullable();
            $table->string('disc_sales_return_amount',100)->nullable();
            $table->string('amex_sales_amount',100)->nullable();
            $table->string('samex_sales_return_amount',100)->nullable();
            $table->string('amex_opt_blue_sales_amount',100)->nullable();
            $table->string('amex_opt_blue_sales_return_amount')->nullable();
            $table->string('voy_sales_amount',100)->nullable();
            $table->string('voy_sales_return_amount',100)->nullable();
            $table->string('debit_sales_amount',100)->nullable();
            $table->string('debit_sales_return_amount',100)->nullable();
            $table->string('wex_sales_amount',100)->nullable();
            $table->string('wex_sales_return_amount',100)->nullable();
            $table->string('acf',100)->nullable();
            $table->string('amex_resid',100)->nullable();
            $table->string('annual_fee',100)->nullable();
            $table->string('assessment',100)->nullable();
            $table->string('auth',100)->nullable();
            $table->string('avs_fees',100)->nullable();
            $table->string('batch_header_fees',100)->nullable();
            $table->string('bill_back',100)->nullable();
            $table->string('bundled_debit',100)->nullable();
            $table->string('chargeback_fees',100)->nullable();
            $table->string('cmc_fees',100)->nullable();
            $table->string('customer_support_fees',100)->nullable();
            $table->string('debit_fees',100)->nullable();
            $table->string('discount_amex',100)->nullable();
            $table->string('discount_amex_opt_blue',100)->nullable();
            $table->string('discount_debit',100)->nullable();
            $table->string('discount_diners',100)->nullable();
            $table->string('discount_disc',100)->nullable();
            $table->string('discount_jcb',100)->nullable();
            $table->string('discount_mc',100)->nullable();
            $table->string('discount_other',100)->nullable();
            $table->string('discount_visa',100)->nullable();
            $table->string('discount_voyager',100)->nullable();
            $table->string('discount_wex',100)->nullable();
            $table->string('ecf',100)->nullable();
            $table->string('monthly_minimum_fees',100)->nullable();
            $table->string('non_receipt_of_pci_validation',100)->nullable();
            $table->string('other',100)->nullable();
            $table->string('pci',100)->nullable();
            $table->string('security',100)->nullable();
            $table->string('statement_fees',100)->nullable();
            $table->string('assmt',100)->nullable();
            $table->string('br_expense',100)->nullable();
            $table->string('mc_interchange',100)->nullable();
            $table->string('visa_interchange',100)->nullable();
            $table->string('disc_interchange',100)->nullable();
            $table->string('amex_interchange',100)->nullable();
            $table->string('amex_opt_blue_interchange',100)->nullable();
            $table->string('voy_interchange',100)->nullable();
            $table->string('debit_interchange',100)->nullable();
            $table->string('wex_interchange',100)->nullable();
            $table->string('mc_interchange_trans',100)->nullable();
            $table->string('visa_interchange_trans',100)->nullable();
            $table->string('disc_interchange_trans',100)->nullable();
            $table->string('amex_interchange_trans',100)->nullable();
            $table->string('amex_opt_blue_interchange_trans',100)->nullable();
            $table->string('voy_interchange_trans',100)->nullable();
            $table->string('debit_interchange_trans',100)->nullable();
            $table->string('wex_interchange_trans',100)->nullable();
            $table->string('gpr',100)->nullable();
            $table->string('rev_share_value',100)->nullable();
            $table->string('adjustment_value',100)->nullable();
            $table->string('collected_fees',100)->nullable();
            $table->string('uncollected_fees',100)->nullable();
            $table->string('net_payout',100)->nullable();
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
        Schema::dropIfExists('temp_residuals');
    }
}
