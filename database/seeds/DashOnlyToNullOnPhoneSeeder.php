<?php

use Illuminate\Database\Seeder;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\Drafts\DraftPartnerContact;
use App\Models\Drafts\DraftPartner;
use App\Models\User;

class DashOnlyToNullOnPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partnerCompany = PartnerCompany::where('phone1','-')
            ->orWhere('phone2','-')
            ->orWhere('mobile_number','-')
            ->get();

        foreach ($partnerCompany as $pc) {
            if ($pc->phone1 == '-') {
                PartnerCompany::where('id', $pc->id)
                    ->update(['phone1' => null]);
            } 
            
            if ($pc->phone2 == '-') {
                PartnerCompany::where('id', $pc->id)
                    ->update(['phone2' => null]);
            } 
            
            if ($pc->mobile_number == '-') {
                PartnerCompany::where('id', $pc->id)
                    ->update(['mobile_number' => null]);
            }
        }

        $partnerContact = PartnerContact::where('mobile_number','-')
            ->orWhere('mobile_number_2','-')
            ->orWhere('other_number','-')
            ->orWhere('other_number_2','-')
            ->get();

        foreach ($partnerContact as $pcon) {
            if ($pcon->mobile_number == '-') {
                PartnerContact::where('id', $pcon->id)
                    ->update(['mobile_number' => null]);
            } 
            
            if ($pcon->mobile_number_2 == '-') {
                PartnerContact::where('id', $pcon->id)
                    ->update(['mobile_number_2' => null]);
            } 
            
            if ($pcon->other_number == '-') {
                PartnerContact::where('id', $pcon->id)
                    ->update(['other_number' => null]);
            } 
            
            if ($pcon->other_number_2 == '-') {
                PartnerContact::where('id', $pcon->id)
                    ->update(['other_number_2' => null]);
            }
        }

        $draftPartnerContact = DraftPartnerContact::where('mobile_number','-')
            ->orWhere('mobile_number_2','-')
            ->orWhere('other_number','-')
            ->orWhere('other_number_2','-')
            ->get();

        foreach ($draftPartnerContact as $dpc) {
            if ($dpc->mobile_number == '-') {
                DraftPartnerContact::where('id', $dpc->id)
                    ->update(['mobile_number' => null]);
            } 
            
            if ($dpc->mobile_number_2 == '-') {
                DraftPartnerContact::where('id', $dpc->id)
                    ->update(['mobile_number_2' => null]);
            } 
            
            if ($dpc->other_number == '-') {
                DraftPartnerContact::where('id', $dpc->id)
                    ->update(['other_number' => null]);
            } 
            
            if ($dpc->other_number_2 == '-') {
                DraftPartnerContact::where('id', $dpc->id)
                    ->update(['other_number_2' => null]);
            }
        }

        $draftPartner = DraftPartner::where('phone1','-')
            ->orWhere('phone2','-')
            ->get();

        foreach ($draftPartner as $dp) {
            if ($dp->phone1 == '-') {
                DraftPartner::where('id', $dp->id)
                    ->update(['phone1' => null]);
            } 
            
            if ($dp->phone2 == '-') {
                DraftPartner::where('id', $dp->id)
                    ->update(['phone2' => null]);
            }
        }

        $user = User::where('mobile_number','-')
            ->orWhere('business_phone1','-')
            ->orWhere('business_phone2','-')
            ->get();

        foreach ($user as $u) {
            if ($u->phone1 == '-') {
                User::where('id', $u->id)
                    ->update(['mobile_number' => null]);
            } 
            
            if ($u->phone2 == '-') {
                User::where('id', $u->id)
                    ->update(['business_phone1' => null]);
            } 
            
            if ($u->phone2 == '-') {
                User::where('id', $u->id)
                    ->update(['business_phone2' => null]);
            }
        }
    }
}