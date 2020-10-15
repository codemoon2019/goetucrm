<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTypesAddHeadid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('user_types', 'head_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->integer('head_id')->default(-1)->nullable();
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
        if (Schema::hasColumn('user_types', 'head_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->dropColumn('head_id');
            });
        }
    }
}
