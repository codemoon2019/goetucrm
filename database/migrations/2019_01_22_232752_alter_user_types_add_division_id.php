<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTypesAddDivisionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('user_types', 'division_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->integer('division_id')->default(-1)->nullable();
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

        if (Schema::hasColumn('user_types', 'division_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                  $table->dropColumn('division_id');
            }); 
        }
        
    }
}
