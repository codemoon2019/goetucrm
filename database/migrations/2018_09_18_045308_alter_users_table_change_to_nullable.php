<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableChangeToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'email_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email_address', 60)->nullable()->change();
                $table->string('mobile_number', 20)->nullable()->change();
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
        if (Schema::hasColumn('users', 'email_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email_address', 60)->change();
                $table->string('mobile_number', 20)->change();
            }); 
        }
    }
}
