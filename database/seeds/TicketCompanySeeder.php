<?php

use App\Models\TicketHeader;
use App\Models\UserType;
use Illuminate\Database\Seeder;

class TicketCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticketHeaders = TicketHeader::where('department', '<>', '-1')->get();
        foreach ($ticketHeaders as $ticketHeader) {
            $companyId = UserType::find($ticketHeader->department)->company_id;
            $ticketHeader->company_id = $companyId;
            $ticketHeader->save();
        }
    }
}
