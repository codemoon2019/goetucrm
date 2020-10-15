<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddApprover extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'confirmed_by')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('confirmed_by')->nullable();
            }); 
        }
        if (!Schema::hasColumn('partners', 'approved_by')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('approved_by')->nullable();
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
        if (Schema::hasColumn('partners', 'confirmed_by')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('confirmed_by');
            }); 
        }
        if (Schema::hasColumn('partners', 'approved_by')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('approved_by');
            }); 
        }
        
    }
}
