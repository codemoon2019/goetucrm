<?php

use Illuminate\Database\Seeder;
use App\Models\PartnerType;
use App\Models\UserType;

class BranchPartnerTypeSeeder extends Seeder
{
    public function run()
    {

        $userType = new UserType;
        $userType->description = 'BRANCH';
        $userType->create_by = 'SYSTEM';
        $userType->update_by = 'Seeder';
        $userType->status = 'A';
        $userType->partner_type_access = '';
        $userType->company_id = -1;
        $userType->parent_id = -1;
        $userType->head_id = -1;
        $userType->display_name = 'BRANCH';
        $userType->division_id = -1;
        $userType->is_chat_support = -1;
        $userType->color = '#000000';
        $userType->save();

        $partnerType = new PartnerType;
        $partnerType->name = 'BRANCH';
        $partnerType->description = 'BRANCH';
        $partnerType->status = 'A';
        $partnerType->user_type_id = $userType->id;
        $partnerType->sequence = 8;
        $partnerType->included_in_partners = 0;
        $partnerType->upline = '1,2,3,4,5,7';
        $partnerType->included_in_agents = 0;
        $partnerType->included_in_leads = 0;
        $partnerType->included_in_training = 0;
        $partnerType->initial = 'B';
        $partnerType->save();

    }
}
