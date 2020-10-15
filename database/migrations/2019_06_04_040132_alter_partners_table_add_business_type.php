<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddBusinessType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'business_type_code')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('business_type_code')->nullable();
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
        if (Schema::hasColumn('partners', 'business_type_code')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('business_type_code');
            }); 
        }
    }
}
