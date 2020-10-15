<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDepartmentsTableAddDisplayName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('user_types', 'display_name')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->text('display_name')->nullable();
            }); 
        }

        $rows = DB::table('user_types')->get(['id', 'description']);
        foreach ($rows as $row) {
            DB::table('user_types')
                ->where('id', $row->id)
                ->update(['display_name' => $row->description]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('user_types', 'display_name')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->dropColumn('display_name');
            }); 
        }
    }
}
