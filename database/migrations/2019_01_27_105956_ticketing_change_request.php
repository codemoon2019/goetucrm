<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TicketingChangeRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_reasons', function (Blueprint $table) {
            $table->increments('id');

            $table->char('code', 10)->nullable();
            $table->string('description', 100)->nullable();

            $table->char('status', 1)->default('A')->nullable();

            $table->string('create_by', 20)->nullable();
            $table->string('update_by', 20)->nullable();
            $table->timestamps();
        });

        Schema::table('ticket_headers', function (Blueprint $table) {
            $table->char('reason', 10)->default('A');
            $table->integer('product_id')->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_reasons');

        if (Schema::hasColumn('ticket_headers', 'product_id')) {
            Schema::table('ticket_headers', function (Blueprint $table) {
                $table->dropColumn('product_id');
            }); 
        }

        if (Schema::hasColumn('ticket_headers', 'reason')) {
            Schema::table('ticket_headers', function (Blueprint $table) {
                $table->dropColumn('reason');
            }); 
        }
    }
}
