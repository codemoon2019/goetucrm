<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductModulesAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_modules', 'status')) {
            Schema::table('product_modules', function (Blueprint $table) {
                $table->string('status',1)->nullable();
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
        if (!Schema::hasColumn('product_modules', 'status')) {
            Schema::table('product_modules', function (Blueprint $table) {
                $table->dropColumn('status')->nullable();
            });
        }
    }
}
