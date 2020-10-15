<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCommissionsAddCommissionBased extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('commissions', 'commission')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('commission');
            });
        }

        if (!Schema::hasColumn('commissions', 'commission_fixed')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->decimal('commission_fixed',18,2)->default(0)->nullable();
            }); 
        }

        if (!Schema::hasColumn('commissions', 'commission_based')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->text('commission_based')->nullable();
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
        if (Schema::hasColumn('commissions', 'commission')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('commission');
            });
        }

        if (Schema::hasColumn('commissions', 'commission_fixed')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('commission_fixed');
            });
        }
        if (Schema::hasColumn('commissions', 'commission_based')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('commission_based');
            });
        }


    }
}
