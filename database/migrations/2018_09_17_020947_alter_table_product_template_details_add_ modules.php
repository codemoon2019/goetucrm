<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductTemplateDetailsAddModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_template_details', 'modules')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->text('modules')->nullable();
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
        if (!Schema::hasColumn('product_template_details', 'modules')) {
            Schema::table('product_template_details', function (Blueprint $table) {
                $table->dropColumn('modules')->nullable();
            });
        }
    }
}
