<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableAddIsOriginalPartner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'is_original_partner')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('is_original_partner')->default(0)->nullable();
            }); 

            $rows = DB::table('users')->where('reference_id','>',0)->get(['id', 'reference_id']);
            foreach ($rows as $row) {
                DB::table('users')
                    ->where('id', $row->id)
                    ->where('username', 'NOT LIKE', 'U%')
                    ->update(['is_original_partner' => 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'is_original_partner')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_original_partner');
            }); 
        }
    }
}
