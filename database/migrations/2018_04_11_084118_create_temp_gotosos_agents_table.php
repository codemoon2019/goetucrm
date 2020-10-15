<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempGotososAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_gotosos_agents', function (Blueprint $table) {
            $table->string('legal_name',500)->nullable();
            $table->string('dba',500)->nullable();
            $table->string('parent_reference_id',50)->nullable();
            $table->string('contact_name',500)->nullable();
            $table->string('email',100)->nullable();
            $table->string('phone_no',100)->nullable();
            $table->string('mobile_number',100)->nullable();
            $table->string('account_no',100)->nullable();
            $table->string('routing_no',100)->nullable();
            $table->string('address',500)->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('website',500)->nullable();
            $table->string('status',100)->nullable();
            $table->string('group_type_id',100)->nullable();
            $table->string('ss_number',100)->nullable();
            $table->string('tax',100)->nullable();
            $table->string('email_unpaid_invoice',100)->nullable();
            $table->string('email_paid_invoice',100)->nullable();
            $table->string('contact_person',100)->nullable();
            $table->string('email_notifier',100)->nullable();
            $table->string('smtp_settings',100)->nullable();
            $table->string('email_notifier_user',100)->nullable();
            $table->string('email_notifier_pass',100)->nullable();
            $table->string('email_notifier_ssl',100)->nullable();
            $table->string('email_notifier_port',100)->nullable();
            $table->string('website2',100)->nullable();
            $table->string('billing_cycle',100)->nullable();
            $table->string('bonus_type',100)->nullable();
            $table->string('sales_rep',100)->nullable();
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
        Schema::dropIfExists('temp_gotosos_agents');
    }
}
