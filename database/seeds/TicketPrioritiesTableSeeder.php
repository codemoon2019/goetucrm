<?php

use Illuminate\Database\Seeder;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class TicketPrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get all ticket priorities from old database
         */
        $ticketPriorities = DB::connection('mysql_old')->table('ticket_priority')->get();

        if(isset($ticketPriorities)){

            foreach($ticketPriorities as $ticketPriority) {

                /**
                 * Insert ticket priorities to new database
                 */
                TicketPriority::create([
                    'id' => $ticketPriority->id,
                    'code' => $ticketPriority->code,
                    'description' => $ticketPriority->description,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'status' => Constant::DEFAULT_STATUS_ACTIVE
                ]);

            }

        }

    }
}
