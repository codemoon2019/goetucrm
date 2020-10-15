<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Product;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',150)->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->default(-1)->nullable();
            $table->string('create_by',80)->nullable();
            $table->string('update_by',80)->nullable()->nullable();
            $table->char('status',1)->default('')->nullable();
            $table->double('buy_rate',18,4)->nullable();
            $table->integer('product_category_id')->nullable();
            $table->string('product_type',20)->default(Product::PRODUCT_TYPE_SERVICE)->nullable();
            $table->integer('sequence')->default(1);
            $table->integer('hide_field')->default(0)->nullable();
            $table->integer('company_id')->default(-1)->nullable();
            $table->integer('product_type_id')->default(-1)->nullable();
            $table->string('field_identifier',100)->nullable();
            $table->integer('is_payment')->default(0)->nullable();
            $table->integer('product_payment_type')->default(-1)->nullable();
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
        Schema::dropIfExists('products');
    }
}
