<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSupplierLeadsTable extends Migration
{
    public function up()
    {
        Schema::table('supplier_lead_products', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('supplier_lead_products', function (Blueprint $table) {
            $table->double('price', 18, 2)->after('description');
        });
    }

    public function down()
    {
        //
    }
}
