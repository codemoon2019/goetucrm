<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_type_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->text('logo')->nullable();
            $table->string('status',1)->nullable();
            $table->string('merchant_mid',80)->nullable();
            $table->string('merchant_processor',100)->nullable();
            $table->string('agent_partner',80)->nullable();
            $table->string('partner_id_reference',80)->nullable();
            $table->string('interested_products',1000)->nullable();
            $table->string('partner_status',50)->nullable();
            $table->integer('original_partner_type_id')->nullable();
            $table->integer('original_parent_id')->nullable();
            $table->string('federal_tax_id',80)->nullable();
            $table->string('credit_card_reference_id',50)->nullable();
            $table->text('services_sold')->nullable();
            $table->string('merchant_url',255)->nullable();
            $table->string('authorized_rep',255)->nullable();
            $table->string('IATA_no',80)->nullable();
            $table->string('tax_filing_name',255)->nullable();
            $table->string('bank_name',255)->nullable();
            $table->string('bank_account_no',255)->nullable();
            $table->string('bank_routing_no',255)->nullable();
            $table->string('bank_account_type_code',80)->nullable();
            $table->string('withdraw_bank_name',255)->nullable();
            $table->string('withdraw_bank_account_no',255)->nullable();
            $table->string('withdraw_bank_routing_no',255)->nullable();
            $table->string('withdraw_bank_account_type_code',80)->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
            $table->bigInteger('copilot_merchant_id')->default(0)->nullable();
            $table->string('cardconnect_profile_id',20)->nullable();
            $table->string('source',50)->nullable();
            $table->integer('is_lead')->default(0)->nullable();
            
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
        Schema::dropIfExists('partners');
    }
}
