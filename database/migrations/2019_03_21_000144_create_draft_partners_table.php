<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draft_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_type_id')->nullable();
            $table->integer('parent_id')->nullable();

            // merchant
            $table->string('merchant_id',80)->nullable();
            $table->integer('is_cc_client')->default(0)->nullable();
            $table->integer('language_id')->nullable();
            $table->integer('auto_emailer')->default(0)->nullable();

            // non-agent
            $table->string('ownership',100)->nullable();
            $table->text('company_name')->nullable();
            $table->string('dba',80)->nullable();
            $table->string('business_date',20)->nullable();
            $table->string('credit_card_reference_id',50)->nullable();
            $table->string('website',200)->nullable();
            
            // agent
            $table->string('business_name',255)->nullable();
            $table->string('tax_id_number',80)->nullable();
            $table->string('bank_name',255)->nullable();
            $table->string('bank_routing_no',255)->nullable();
            $table->string('bank_dda',255)->nullable();

            // business
            $table->text('business_address1')->nullable();
            $table->text('business_address2')->nullable();
            $table->string('business_country',100)->nullable();
            $table->string('business_state',100)->nullable();
            $table->string('business_city',100)->nullable();
            $table->string('business_zip',100)->nullable();
            
            // billing
            $table->text('billing_address')->nullable();
            $table->text('billing_address2')->nullable();   
            $table->string('billing_country',100)->nullable();
            $table->string('billing_state',100)->nullable();
            $table->string('billing_city',100)->nullable();
            $table->string('billing_zip',100)->nullable();
            
            // non-agent & agent
            $table->string('phone1',20)->nullable();
            $table->string('partner_email',100)->nullable();

            // non-agent
            $table->string('phone2',20)->nullable();
            $table->string('extension',20)->nullable();
            $table->string('extension_2',20)->nullable();
            $table->string('partner_fax',20)->nullable();

            // agent
            $table->string('email_notifier',80)->nullable();

            // mailing
            $table->text('mailing_address')->nullable();
            $table->text('mailing_address2')->nullable();
            $table->string('mailing_city',100)->nullable();
            $table->string('mailing_state',100)->nullable();
            $table->string('mailing_zip',100)->nullable();
            $table->string('mailing_country',100)->nullable();

            // dba
            $table->text('dba_address')->nullable();
            $table->text('dba_address2')->nullable();   
            $table->string('dba_country',100)->nullable();
            $table->string('dba_state',100)->nullable();
            $table->string('dba_city',100)->nullable();
            $table->string('dba_zip',100)->nullable();

            // shipping
            $table->text('shipping_address')->nullable();
            $table->text('shipping_address2')->nullable();   
            $table->string('shipping_country',100)->nullable();
            $table->string('shipping_state',100)->nullable();
            $table->string('shipping_city',100)->nullable();
            $table->string('shipping_zip',100)->nullable();

            // billing cycle merchant & agent
            $table->string('billing_cycle',80)->nullable();
            $table->string('billing_month',80)->nullable();
            $table->string('billing_day',80)->nullable();

            // leads & prospects
            $table->string('merchant_processor',100)->nullable();
            $table->text('comment')->nullable();
            $table->string('interested_products',1000)->nullable();

            $table->integer('is_stored_to_partners')->default(0)->nullable();
            
            // $table->string('status',1)->default('A')->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('draft_partners');
    }
}
