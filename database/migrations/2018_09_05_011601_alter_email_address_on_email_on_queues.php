<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailAddressOnEmailOnQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('email_on_queues', 'email_address')) {
            Schema::table('email_on_queues', function (Blueprint $table) {
                $table->text('email_address')->change();
            }); 
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    // if (Schema::hasColumn('email_on_queues', 'email_address')) {
    //     Schema::table('email_on_queues', function (Blueprint $table) {
    //         $table->string('email_address',200)->change();
    //     }); 
    // }
    }
}
