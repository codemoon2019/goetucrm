<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDivisionAddAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('divisions', 'address')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->text('address')->nullable();
            }); 
        }

        if (!Schema::hasColumn('divisions', 'country')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->string('country',100)->nullable();
            }); 
        }

        if (!Schema::hasColumn('divisions', 'city')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->string('city',100)->nullable();
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
        if (Schema::hasColumn('divisions', 'address')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
        if (Schema::hasColumn('divisions', 'country')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
        if (Schema::hasColumn('divisions', 'city')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('city');
            });
        }
    }
}
