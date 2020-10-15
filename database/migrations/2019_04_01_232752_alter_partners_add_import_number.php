<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PartnerType;

class AlterPartnersAddImportNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('partners', 'import_number')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->integer('import_number')->nullable();
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
        if (Schema::hasColumn('partners', 'import_number')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('import_number');
            }); 
        }
    }
}
