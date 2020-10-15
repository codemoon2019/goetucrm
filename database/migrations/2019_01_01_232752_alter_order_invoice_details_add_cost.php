<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrderInvoiceDetailsAddCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('invoice_details', 'cost')) {
            Schema::table('invoice_details', function (Blueprint $table) {
                $table->decimal('cost',18,2)->default(0.00)->nullable();
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

        if (Schema::hasColumn('invoice_details', 'cost')) {
            Schema::table('invoice_details', function (Blueprint $table) {
                $table->dropColumn('cost');
            }); 
        }
        
    }
}
