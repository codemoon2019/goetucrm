<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddDisplayCountryOnToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'display_on_partner')) {
                $table->smallInteger('display_on_partner')->default(0);
            }
            if (!Schema::hasColumn('countries', 'display_on_merchant')) {
                $table->smallInteger('display_on_merchant')->default(0);
            }
            if (!Schema::hasColumn('countries', 'display_on_others')) {
                $table->smallInteger('display_on_others')->default(0);
            }
            if (!Schema::hasColumn('countries', 'display_on_users')) {
                $table->smallInteger('display_on_users')->default(0);
            }
        });

        Artisan::call('db:seed', [
            '--class' => UpdateDisplayOnToCountriesTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('display_on_partner');                                    
            $table->dropColumn('display_on_merchant');                                    
            $table->dropColumn('display_on_others');                                    
            $table->dropColumn('display_on_users');                                    
        });
    }
}
