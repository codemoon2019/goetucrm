<?php

use Illuminate\Database\Seeder;
use App\Models\TicketHeader;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class TicketHeadersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get ticket headers from old database
         */
        $ticketHeaders = DB::connection('mysql_old')->table('ticket_header')->get();

        if (isset($ticketHeaders)) {
            foreach ($ticketHeaders as $ticketHeader) {

                /**
                 * Insert all ticket headers to new database
                 */
                TicketHeader::create([
                    'id' => $ticketHeader->id,
                    'subject' => $ticketHeader->subject,
                    'partner_id' => $ticketHeader->partner_id,
                    'product_id' => $ticketHeader->product_id,
                    'status' => $ticketHeader->status,
                    'type' => $ticketHeader->type,
                    'priority' => $ticketHeader->priority,
                    'department' => $ticketHeader->department,
                    'due_date' => $ticketHeader->due_date,
                    'description' => $ticketHeader->description,
                    'attachment' => $ticketHeader->attachment,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'update_by' => null,
                    'delete_by' => null,
                    'delete_date' => null,
                    'is_deleted' => $ticketHeader->is_deleted,
                    'assignee' => null,
                    'is_starred' => $ticketHeader->is_starred,
                    'reference_id' => $ticketHeader->reference_id,
                    'remarks' => $ticketHeader->remarks,
                    'parent_id' => $ticketHeader->parent_id,
                    'pmx_flag' => $ticketHeader->pmx_flg,
                    'pmx_attachment' => $ticketHeader->pmx_attachment
                ]);

            }
        }

    }
}
