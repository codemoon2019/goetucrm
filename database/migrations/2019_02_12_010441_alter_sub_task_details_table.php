<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubTaskDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE sub_task_details ALTER status SET DEFAULT "T"');

        /** @note Code below does not work as of writing. Laravel issue */
        /* Schema::table('sub_task_details', function (Blueprint $table) {
            $table->char('status',1)->nullable()->default('T')->change();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sub_task_details ALTER status SET DEFAULT NULL');
    }
}
