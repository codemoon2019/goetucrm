<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('user_types', 'company_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->integer('company_id')->default(-1)->nullable();
                $table->integer('parent_id')->default(-1)->nullable();
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
        if (Schema::hasColumn('user_types', 'company_id')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->dropColumn('company_id');
                $table->dropColumn('parent_id');
            });
        }
    }
}
