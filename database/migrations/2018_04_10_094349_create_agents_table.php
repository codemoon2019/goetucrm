<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iso_id');
            $table->string('agent_code',60);
            $table->string('agent_name',100);
            $table->string('legal_name',100);
            $table->string('dba_name',100);
            $table->string('tax_id',30);
            $table->string('phone_no',20);
            $table->string('reseller_name',100);
            $table->string('reseller_type',80);
            $table->string('fax',20);
            $table->text('dba_address1');
            $table->text('dba_address2');
            $table->string('dba_city',80);
            $table->string('dba_state',80);
            $table->string('dba_zip',80);
            $table->string('dba_country',80);
            $table->text('mailing_address1');
            $table->text('mailing_address2');
            $table->string('mailing_city',80);
            $table->string('mailing_state',80);
            $table->string('mailing_zip',80);
            $table->string('mailing_country',80);
            $table->string('account_no',80);
            $table->string('routing_no',80);
            $table->string('email',100);
            $table->string('create_by',80);
            $table->string('update_by',80);
            $table->string('status',1);
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
        Schema::dropIfExists('agents');
    }
}
