<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BusinessIndustryFeatureChanges extends Migration
{
    public function up()
    {
        Schema::table('supplier_leads', function (Blueprint $table) {
            $table->string('business_type_code')
                ->nullable()
                ->after('business_name');
        }); 
    }

    public function down()
    {
        if (Schema::hasColumn('supplier_leads', 'business_type_code')) {
            Schema::table('supplier_leads', function (Blueprint $table) {
                $table->dropColumn('business_type_code');
            }); 
        }
    }
}
