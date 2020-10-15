<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PartnerType;

class AlterProductOrdersAddPreferredPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('product_orders', 'preferred_payment')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->string('preferred_payment')->default('Cash')->nullable();
                $table->timestamp('date_sent')->nullable();
                $table->timestamp('date_receive')->nullable();
                $table->timestamp('date_signed')->nullable();
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
        if (Schema::hasColumn('product_orders', 'preferred_payment')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->dropColumn('preferred_payment');
                $table->dropColumn('date_sent');
                $table->dropColumn('date_receive');
                $table->dropColumn('date_signed');
            }); 
        }
    }
}
