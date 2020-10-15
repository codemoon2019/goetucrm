<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAddedToOcalToCalendarActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_activities', function (Blueprint $table) {
            $table->integer('is_added_to_ocal')->default('0');                        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_activities', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ocal');                        
        });
    }
}
