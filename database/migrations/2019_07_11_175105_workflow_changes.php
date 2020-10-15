<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkflowChanges extends Migration
{
    public function up()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->boolean('default_workflow')->nullable()->after('product_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->boolean('default_workflow')->nullable()->after('product_id');
        });

        Schema::table('sub_task_details', function(Blueprint $table) {
            $table->dropForeign('sub_task_details_priority_id_foreign');
            $table->dropColumn('priority_id');
        });

        Schema::table('sub_task_details', function(Blueprint $table) {
            $table->char('ticket_priority_code', 10)->nullable();
        });

        Schema::table('sub_task_template_details', function(Blueprint $table) {
            $table->dropForeign('sub_task_template_details_priority_id_foreign');
            $table->dropColumn('priority_id');
        });

        Schema::table('sub_task_template_details', function(Blueprint $table) {
            $table->char('ticket_priority_code', 10)->nullable();
        });
    }

    public function down()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->dropColumn('default_workflow')->nullable();
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->dropColumn('default_workflow')->nullable();
        });
    }
}
