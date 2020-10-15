<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TicketConfigurationChangesV2 extends Migration
{
    public function up()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('product_id');
        });

        Schema::table('ticket_types', function(Blueprint $table) {
            $table->unsignedInteger('product_id')
                ->nullable()
                ->after('company_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('product_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->unsignedInteger('product_id')
                ->nullable()
                ->after('company_id');
        });

        Schema::table('ticket_headers', function(Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('reason');
        });

        Schema::table('ticket_headers', function(Blueprint $table) {
            $table->unsignedInteger('type')->after('email_message_id');
            $table->unsignedInteger('reason')->after('type');
        });
    }

    public function down()
    {
        Schema::table('ticket_types', function(Blueprint $table) {
            $table->dropColumn('product_id');
        });

        Schema::table('ticket_types', function(Blueprint $table) {
            $table->unsignedInteger('product_id')->after('company_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->dropColumn('product_id');
        });

        Schema::table('ticket_reasons', function(Blueprint $table) {
            $table->unsignedInteger('product_id')->after('company_id');
        });

        Schema::table('ticket_headers', function(Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('priority');
        });

        Schema::table('ticket_headers', function(Blueprint $table) {
            $table->char('type',10)->nullable();
            $table->char('priority',10)->nullable();
        });
    }
}
