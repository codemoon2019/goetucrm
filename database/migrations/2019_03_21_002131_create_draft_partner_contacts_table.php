<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftPartnerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draft_partner_contacts', function (Blueprint $table) {
            $table->increments('id');
            
            // contact
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('middle_name',100)->nullable();
            $table->string('position',100)->nullable();
            $table->string('ssn',20)->nullable();
            $table->string('ownership_percentage',3)->nullable();
            $table->dateTime('dob')->nullable();
            
            // contact address
            $table->text('contact_address1')->nullable();
            $table->text('contact_address2')->nullable();
            $table->string('contact_city',100)->nullable();
            $table->string('contact_state',100)->nullable();
            $table->string('contact_zip',100)->nullable();
            $table->string('contact_country',100)->nullable();
            
            // contact' contact info
            $table->string('other_number',20)->nullable();
            $table->string('other_number_2',20)->nullable();
            $table->string('contact_fax',20)->nullable();
            $table->string('mobile_number',20)->nullable();
            $table->string('mobile_number_2',20)->nullable();
            $table->string('contact_email',100)->nullable();

            $table->integer('is_original_contact')->nullable();
            
            $table->unsignedInteger('draft_partner_id');
            $table->foreign('draft_partner_id')
                ->references('id')
                ->on('draft_partners')
                ->onDelete('cascade');
            
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
        Schema::dropIfExists('draft_partner_contacts');
    }
}
