<?php

use App\Models\Ownership;
use App\Models\PartnerCompany;
use Illuminate\Database\Seeder;

class UpdateOwnershipOnPartnerCompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        PartnerCompany::chunk(100, function($partnerCompanies){
            $ownerships = Ownership::where('status','A')->get();

            foreach ($partnerCompanies as $pc) {
                foreach ($ownerships as $o) {
                    if ($pc->ownership == $o->name) {
                        $pc->update(['ownership' => $o->code]);
                    }
                }
            }
        });
    }
}
