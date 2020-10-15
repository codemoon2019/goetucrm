<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartnersTableAddBillingStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('partners', 'billing_status')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('billing_status')->default('Active')->nullable();
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
        if (Schema::hasColumn('partners', 'billing_status')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('billing_status');
            }); 
        }
    }
}
