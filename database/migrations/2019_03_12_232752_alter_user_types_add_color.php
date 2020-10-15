<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PartnerType;

class AlterUserTypesAddColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('user_types', 'color')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->string('color')->default('#000000')->nullable();
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
        if (Schema::hasColumn('user_types', 'color')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->dropColumn('color');
            }); 
        }
    }
}
