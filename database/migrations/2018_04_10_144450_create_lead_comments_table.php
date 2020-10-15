<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id')->nullable();
            $table->text('comment')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('status',1)->default('A');
            $table->integer('user_id')->nullable();
            $table->integer('is_public')->default(1);
            $table->text('attachment')->nullable();
            $table->integer('is_internal')->default(0); 
            $table->string('lead_status',50)->nullable();
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
        Schema::dropIfExists('lead_comments');
    }
}
