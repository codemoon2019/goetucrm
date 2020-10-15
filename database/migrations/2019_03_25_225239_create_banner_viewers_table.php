<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerViewersTable extends Migration
{
    public function up()
    {
        Schema::create('banner_viewers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('banner_id');
            $table->string('viewer_type');
            $table->unsignedInteger('viewer_id')->nullable();
            $table->string('status', 1)->default('A');
            $table->string('create_by'); /** username in users table */
            $table->string('update_by'); /** username in users table */
            $table->timestamps();

            $table->foreign('banner_id')
                ->references('id')
                ->on('banners')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('banner_viewers', function (Blueprint $table) {
            $table->dropForeign('banner_viewers_banner_id_foreign');
        });

        Schema::dropIfExists('banner_viewers');
    }
}
