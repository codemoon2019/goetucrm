<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductOrderCommentsAddAttachmentAccessIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_order_comments', 'attachment_access_ids')) {
            Schema::table('product_order_comments', function (Blueprint $table) {
                $table->text('attachment_access_ids')->nullable();
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
        if (!Schema::hasColumn('product_order_comments', 'attachment_access_ids')) {
            Schema::table('product_order_comments', function (Blueprint $table) {
                $table->dropColumn('attachment_access_ids')->nullable();
            });
        }
    }
}
