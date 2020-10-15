<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('partner_id');
            $table->dateTime('invoice_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('total_due',18,2)->nullable();
            $table->decimal('balance',18,2)->nullable();
            $table->string('transaction_id',50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('reference',255)->nullable();
            $table->integer('process_now')->nullable();
            $table->string('return_reason_code',20)->nullable();
            $table->string('return_info',255)->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('status',1)->nullable();
            $table->integer('is_exported')->nullable();
            $table->dateTime('export_date')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('recurred')->nullable();
            $table->text('filename')->nullable();
            $table->integer('is_processed')->nullable();
            $table->text('failed_response_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_headers');
    }
}
