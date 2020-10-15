<?php

use App\Models\ApiKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiKeysTable extends Migration
{
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_name');
            $table->string('key')->unique();
            $table->text('bearer_token')->nullable();
            $table->unsignedInteger('user_id');
            $table->text('note')->nullable();
            $table->string('status', 1)->default(ApiKey::STATUS_ACTIVE);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropForeign('api_keys_user_id_foreign');
        });

        Schema::dropIfExists('api_keys');
    }
}
