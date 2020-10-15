<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PartnerType;

class AlterPartnerTypesAddDisplayName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('partner_types', 'display_name')) {
            Schema::table('partner_types', function (Blueprint $table) {
                $table->string('display_name')->nullable();
            }); 
        }
        PartnerType::where('description', 'COMPANY')
            ->update(['display_name' => 'Company']);  
        PartnerType::where('description', 'ISO')
            ->update(['display_name' => 'I Partner']);
        PartnerType::where('description', 'SUB ISO')
            ->update(['display_name' => 'SI Partner']);
        PartnerType::where('description', 'AGENT')
            ->update(['display_name' => 'A Partner']);
        PartnerType::where('description', 'SUB AGENT')
            ->update(['display_name' => 'SA Partner']);   

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('partner_types', 'display_name')) {
            Schema::table('partner_types', function (Blueprint $table) {
                  $table->dropColumn('display_name');
            }); 
        }
    }
}
