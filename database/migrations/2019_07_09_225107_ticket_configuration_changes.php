<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TicketConfigurationChanges extends Migration
{
    public function up()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->unsignedInteger('company_id')->after('description');
            $table->unsignedInteger('product_id')->after('company_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->dropForeign('ticket_reasons_ticket_priority_id_foreign');
            $table->dropColumn('ticket_priority_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) { 
            $table->unsignedInteger('company_id')->after('description');
            $table->unsignedInteger('product_id')->after('company_id');
            $table->unsignedInteger('department_id')->after('company_id');
            $table->unsignedInteger('ticket_type_id')->after('department_id');
            $table->char('ticket_priority_code', 10)->after('ticket_type_id');
        });
    }

    public function down()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->dropColumn('company_id')->after('description');
            $table->dropColumn('product_id')->after('description');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('product_id');
            $table->dropColumn('department_id');
            $table->dropColumn('ticket_type_id');
            $table->dropColumn('ticket_priority_code');
        });

        Schema::table('ticket_reasons', function (Blueprint $table) {
            $table->unsignedInteger('ticket_priority_id')
                ->nullable()
                ->default(null);
                
            $table->foreign('ticket_priority_id')
                ->references('id')
                ->on('ticket_priorities');
        });
    }
}
