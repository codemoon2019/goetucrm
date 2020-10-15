<?php

use App\Models\TicketPriority;
use App\Models\TicketReason;
use Illuminate\Database\Seeder;

class TicketReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketReason::truncate();

        $data = [
            [
                'code' => 'A',
                'description' => 'Gift card is not working',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'B',
                'description' => 'Terminal password is not working',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'C',
                'description' => 'Lost gift card',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'D',
                'description' => 'Refund request',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'E',
                'description' => 'Gift card balance inquiry',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'F',
                'description' => 'Online order not working',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'G',
                'description' => 'Not receiving orders in fax',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'H',
                'description' => 'Phone not ringing for orders',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'I',
                'description' => 'No email received for orders',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'J',
                'description' => 'Order cancelation',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'K',
                'description' => 'Cancel OLO',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::HIGH_ID,
            ],
            [
                'code' => 'L',
                'description' => 'Menu Update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'M',
                'description' => 'Business hours update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'N',
                'description' => 'Change of Address',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'O',
                'description' => 'Website Update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'P',
                'description' => 'Recharge/Add tip',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
            [
                'code' => 'Q',
                'description' => 'Rewards Update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::LOW_ID,
            ],
            [
                'code' => 'R',
                'description' => 'Promotion Update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::LOW_ID,
            ],
            [
                'code' => 'S',
                'description' => 'Coupon Update',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::LOW_ID,
            ],
            [
                'code' => 'T',
                'description' => 'Domain Transfer',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'ticket_priority_id' => TicketPriority::MEDIUM_ID,
            ],
        ];

        TicketReason::insert($data);
    }
}
