<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TicketDetail;
use App\Contracts\Constant;

class TicketDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get ticket details from old database
         */
        $details = DB::connection('mysql_old')->table('ticket_detail')->get();

        if (isset($details)) {

            foreach ($details as $detail) {

                /**
                 * Insert all ticket details to new database
                 */
                TicketDetail::create([
                    'id' => $detail->id,
                    'ticket_id' => $detail->ticket_id,
                    'message' => $detail->message,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'attachment' => $detail->attachment,
                    'message_type' => $detail->message_type,
                    'pmx_flg' => $detail->pmx_flg,
                    'pmx_attachment' => (!empty($detail->pmx_attachment) ? $detail->pmx_attachment : null)
                ]);

            }

        }

    }
}
