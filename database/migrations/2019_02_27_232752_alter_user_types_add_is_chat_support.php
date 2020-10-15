<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTypesAddIsChatSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('user_types', 'is_chat_support')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->integer('is_chat_support')->default(-1)->nullable();
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

        if (Schema::hasColumn('user_types', 'is_chat_support')) {
            Schema::table('user_types', function (Blueprint $table) {
                  $table->dropColumn('is_chat_support');
            }); 
        }
        
    }
}
