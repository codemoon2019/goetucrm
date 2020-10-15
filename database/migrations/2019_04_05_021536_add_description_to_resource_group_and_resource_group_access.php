<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToResourceGroupAndResourceGroupAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('resource_groups', 'description')) {
            Schema::table('resource_groups', function (Blueprint $table) {
                $table->text('description')->nullable();
            }); 
        } 

        if (!Schema::hasColumn('resource_group_accesses', 'description')) {
            Schema::table('resource_group_accesses', function (Blueprint $table) {
                $table->text('description')->nullable();
            }); 
        } 

        if (Schema::hasColumn('resource_groups', 'update_by')) {
            Schema::table('resource_groups', function (Blueprint $table) {
                $table->string('update_by',50)->change();
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
        if (Schema::hasColumn('resource_groups', 'description')) {
            Schema::table('resource_groups', function (Blueprint $table) {
                $table->dropColumn('description');
            }); 
        }

        if (Schema::hasColumn('resource_group_accesses', 'description')) {
            Schema::table('resource_group_accesses', function (Blueprint $table) {
                $table->dropColumn('description');
            }); 
        }
    }
}
