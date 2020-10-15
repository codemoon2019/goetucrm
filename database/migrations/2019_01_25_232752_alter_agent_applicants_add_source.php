<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAgentApplicantsAddSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('agent_applicants', 'source')) {
            Schema::table('agent_applicants', function (Blueprint $table) {
                $table->text('source')->nullable();
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

        if (Schema::hasColumn('agent_applicants', 'source')) {
            Schema::table('agent_applicants', function (Blueprint $table) {
                  $table->dropColumn('source');
            }); 
        }
        
    }
}
