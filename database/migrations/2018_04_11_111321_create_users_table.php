<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username',60);
            $table->string('password',60);
            $table->string('last_name',60);
            $table->string('first_name',60);
            $table->string('email_address',60);
            $table->string('mobile_number',20);
            $table->string('user_type_id',100);
            $table->integer('reference_id')->nullable()->default('-1');
            $table->string('create_by',60)->nullable();
            $table->string('update_by',60)->nullable();
            $table->char('status',1);
            $table->smallInteger('is_iso')->default(0)->nullable();
            $table->smallInteger('is_merchant')->default(0)->nullable();
            $table->smallInteger('is_customer')->default(0)->nullable();
            $table->smallInteger('is_admin')->default(0)->nullable();
            $table->smallInteger('is_agent')->default(0)->nullable();
            $table->smallInteger('is_not_default')->default(0)->nullable();
            $table->integer('require_change_password')->default(1)->nullable();
            $table->integer('allow_status_override')->default(0)->nullable();
            $table->integer('is_verified_email')->default(0)->nullable();
            $table->integer('is_verified_mobile')->default(0)->nullable();
            $table->string('ein',20)->nullable();
            $table->string('ssn',20)->nullable();
            $table->string('business_address1',100)->nullable();
            $table->string('business_address2',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',20)->nullable();
            $table->string('zip',20)->nullable();
            $table->string('country',100)->nullable();
            $table->string('business_phone1',20)->nullable();
            $table->string('extension',20)->nullable();
            $table->string('business_phone2',20)->nullable();
            $table->string('fax',20)->nullable();
            $table->string('mail_address1',100)->nullable();
            $table->string('mail_address2',100)->nullable();
            $table->string('mail_city',100)->nullable();
            $table->string('mail_state',20)->nullable();
            $table->string('mail_zip',20)->nullable();
            $table->string('mail_country',100)->nullable();
            $table->string('home_address1',100)->nullable();
            $table->string('home_address2',100)->nullable();
            $table->string('home_city',100)->nullable();
            $table->string('home_state',20)->nullable();
            $table->string('home_zip',20)->nullable();
            $table->string('home_country',100)->nullable();
            $table->string('home_landline',20)->nullable();
            $table->string('country_code',20)->nullable();
            $table->date('dob')->nullable();
            $table->string('favorites',60)->nullable();
            $table->integer('is_partner')->default(1)->nullable();
            $table->smallInteger('is_online')->default(0)->nullable();
            $table->string('temp_socket_id',255)->nullable();
            $table->string('remember_token',255)->nullable();
            $table->dateTime('last_activity')->nullable();
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
        Schema::dropIfExists('users');
    }
}
