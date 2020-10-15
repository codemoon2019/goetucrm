<?php

use Illuminate\Database\Seeder;
use App\Models\TicketFilterCreation;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class TicketFilterCreationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get ticket filter creations from old database
         */
        $ticketFilterCreations = DB::connection('mysql_old')->table('ticket_filter_creation')->get();

        if (isset($ticketFilterCreations)) {

            /**
             * Get created by remarks
             */
            $getCreatedByRemarks = \App\Services\Utility\Helper::generateCreated(true);

            foreach ($ticketFilterCreations as $key => $ticketFilterCreation) {

                /**
                 * Insert new ticket filter creation in new database
                 */
                TicketFilterCreation::create([
                    'id' => $ticketFilterCreation->id,
                    'description' => $ticketFilterCreation->description,
                    'remarks' => $ticketFilterCreation->remarks,
                    'sequence' => $ticketFilterCreation->sequence,
                    'query_sequence' => $ticketFilterCreation->query_sequence,
                    'status' => $ticketFilterCreation->status,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'new_remarks' => $getCreatedByRemarks[$key]
                ]);
            }
        }

    }
}
