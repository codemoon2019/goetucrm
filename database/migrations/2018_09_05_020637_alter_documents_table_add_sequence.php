<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentsTableAddSequence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('documents', 'sequence')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->integer('sequence')->default(-1)->nullable();
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
        if (!Schema::hasColumn('documents', 'sequence')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->drop('sequence');
            });
        }
    }
}
