<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\ResourceGroupAccess;

class UpdateResourceGroupAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ResourceGroupAccess::whereIn('id',Array(116,119))->update(array('status' => 'I'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ResourceGroupAccess::whereIn('id',Array(116,119))->update(array('status' => 'A'));
    }
}
