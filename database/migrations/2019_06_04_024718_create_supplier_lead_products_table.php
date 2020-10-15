<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierLeadProductsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_lead_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('price');

            $table->unsignedInteger('supplier_lead_id');
            $table->foreign('supplier_lead_id')
                ->references('id')
                ->on('supplier_leads');
                
            $table->string('status', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('supplier_lead_products');
        Schema::enableForeignKeyConstraints();
    }
}
