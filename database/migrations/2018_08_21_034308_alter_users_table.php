<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableVersion2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('company_id')->default(-1)->nullable();
            }); 
        }

        $rows = DB::table('users')->where('reference_id','>',0)->get(['id', 'reference_id']);
        foreach ($rows as $row) {
            DB::table('users')
                ->where('id', $row->id)
                ->update(['company_id' => $row->reference_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('company_id');
            }); 
        }
    }
}
