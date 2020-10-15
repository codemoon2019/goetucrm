<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDraftPartnersTableAddMerchantUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('draft_partners', 'merchant_url')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->string('merchant_url')->nullable();
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
        if (Schema::hasColumn('draft_partners', 'merchant_url')) {
            Schema::table('draft_partners', function (Blueprint $table) {
                $table->dropColumn('merchant_url');
            }); 
        }
    }
}
