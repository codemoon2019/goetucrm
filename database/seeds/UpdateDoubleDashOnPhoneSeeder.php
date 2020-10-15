<?php

use Illuminate\Database\Seeder;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\User;

class UpdateDoubleDashOnPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partnerCompany = PartnerCompany::where('phone1','like','--%')
            ->orWhere('phone2','like','--%')
            ->orWhere('mobile_number','like','--%')
            ->get();

        foreach ($partnerCompany as $pc) {
            if (strpos($pc->phone1,'--') !== false) {
                $phone1 = substr($pc->phone1, 1, strlen($pc->phone1));
                PartnerCompany::where('id', $pc->id)
                    ->update(['phone1' => $phone1]);
            } 
            
            if (strpos($pc->phone2,'--') !== false) {
                $phone2 = substr($pc->phone2, 1, strlen($pc->phone2));
                PartnerCompany::where('id', $pc->id)
                    ->update(['phone2' => $phone2]);
            } 
            
            if (strpos($pc->mobile_number,'--') !== false) {
                $mobile_number = substr($pc->mobile_number, 1, strlen($pc->mobile_number));
                PartnerCompany::where('id', $pc->id)
                    ->update(['mobile_number' => $mobile_number]);
            }
        }

        $partnerContact = PartnerContact::where('other_number','like','--%')
            ->orWhere('other_number_2','like','--%')
            ->orWhere('mobile_number','like','--%')
            ->get();

        foreach ($partnerContact as $pcon) {
            if (strpos($pcon->other_number,'--') !== false) {
                $other_number = substr($pcon->other_number, 1, strlen($pcon->other_number));
                PartnerContact::where('id', $pcon->id)
                    ->update(['other_number' => $other_number]);
            } 
            
            if (strpos($pcon->other_number_2,'--') !== false) {
                $other_number_2 = substr($pcon->other_number_2, 1, strlen($pcon->other_number_2));
                PartnerContact::where('id', $pcon->id)
                    ->update(['other_number_2' => $other_number_2]);
            } 
            
            if (strpos($pcon->mobile_number,'--') !== false) {
                $mobile_number = substr($pcon->mobile_number, 1, strlen($pcon->mobile_number));
                PartnerContact::where('id', $pcon->id)
                    ->update(['mobile_number' => $mobile_number]);
            }
        }

        $user = User::where('business_phone1','like','--%')
            ->orWhere('mobile_number','like','--%')
            ->get();

        foreach ($user as $u) {
            if (strpos($u->business_phone1,'--') !== false) {
                $business_phone1 = substr($u->business_phone1, 1, strlen($u->business_phone1));
                User::where('id', $u->id)
                    ->update(['business_phone1' => $business_phone1]);
            } 
            
            if (strpos($u->mobile_number,'--') !== false) {
                $mobile_number = substr($u->mobile_number, 1, strlen($u->mobile_number));
                User::where('id', $u->id)
                    ->update(['mobile_number' => $mobile_number]);
            }
        }
    }
}
