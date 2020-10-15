<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubtaskTemplateDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('sub_task_template_details', function (Blueprint $table) {
            $table->unsignedInteger('priority_id')->default(3);
            $table->foreign('priority_id')
                ->references('id')
                ->on('ticket_priorities');
        });
    }

    public function down()
    {
        Schema::table('sub_task_template_details', function (Blueprint $table) {
            $table->dropForeign('sub_task_template_details_priority_id_foreign');
            $table->dropColumn('priority_id');
        });
    }
}
