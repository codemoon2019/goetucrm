<?php

use App\Models\Partner;
use App\Models\TicketReason;
use App\Models\TicketType as TicketIssueType;
use App\Models\UserType as Department;
use App\Services\Tickets\TicketSetup;
use Illuminate\Database\Seeder;

class TicketConfigurationSeeder extends Seeder
{
    public function run()
    {
        TicketIssueType::truncate();
        TicketReason::truncate();

        $partners = Partner::whereHas('partnerCompany')
            ->where('partner_type_id', 7)
            ->get();

        foreach ($partners as $partner) {
            $department = Department::whereHas('partnerCompany')
                ->where('company_id', $partner->id)
                ->first();

            if (!isset($department))
                continue;

            $ticketSetup = new TicketSetup;
            $ticketSetup->setup($partner->id, $department->id);
        }
    }
}
