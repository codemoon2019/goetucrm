<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER tr_update_partner_status_log AFTER UPDATE ON `partners` FOR EACH ROW

            BEGIN
            IF NEW.status <=> OLD.status THEN

                INSERT INTO partner_status_logs (`partner_id`, `status`, `update_by`, `created_at`, `updated_at`) 
                VALUES (OLD.id, NEW.status, NEW.update_by, now(),now());

            END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_update_partner_status_log`');    }
}
