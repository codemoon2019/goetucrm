<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFormFieldsToPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('social_security_id',80)->nullable();
            $table->string('tax_id_number',80)->nullable();
            $table->string('bank_dda',255)->nullable();
            $table->string('email_notifier',80)->nullable();
            $table->integer('email_unpaid_invoice')->default(0)->nullable();
            $table->integer('email_paid_invoice')->default(0)->nullable();
            $table->integer('smtp_settings')->default(0)->nullable();
            $table->string('billing_cycle',80)->nullable();
            $table->string('billing_month',80)->nullable();
            $table->string('billing_day',80)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('social_security_id');
            $table->dropColumn('tax_id_number');
            $table->dropColumn('bank_dda');
            $table->dropColumn('email_notifier');
            $table->dropColumn('email_unpaid_invoice');
            $table->dropColumn('email_paid_invoice');
            $table->dropColumn('smtp_settings');
            $table->dropColumn('billing_cycle');
            $table->dropColumn('billing_month');
            $table->dropColumn('billing_day');
        });
    }
}
