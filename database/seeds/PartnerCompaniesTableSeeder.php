<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;
use App\Models\PartnerCompany;

class PartnerCompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partnerCompanies = DB::connection("mysql_old")->table('partner_company')->get();

        /**
         * Check if partner company is not empty or have a collection
         */
        if (isset($partnerCompanies)) {

            foreach ($partnerCompanies as $partnerCompany) {

                /**
                 * Partner company created
                 */
                PartnerCompany::create([
                    'partner_id' => $partnerCompany->partner_id,
                    'company_name' => $partnerCompany->company_name,
                    'address1' => $partnerCompany->address1,
                    'address2' => $partnerCompany->address2,
                    'city' => $partnerCompany->city,
                    'state' => $partnerCompany->state,
                    'zip' => $partnerCompany->zip,
                    'country' => $partnerCompany->country,
                    'email' => $partnerCompany->email,
                    'phone1' => $partnerCompany->phone1,
                    'phone2' => $partnerCompany->phone2,
                    'dba' => $partnerCompany->dba,
                    'fax' => $partnerCompany->fax,
                    'mobile_number' => $partnerCompany->mobile_number,
                    'ownership' => $partnerCompany->ownership,
                    'country_code' => $partnerCompany->country_code,
                    'website' => $partnerCompany->website,
                    'ssn' => $partnerCompany->ssn,
                    'business_date' => $partnerCompany->business_date,
                    'extension' => $partnerCompany->extension
//                    'extension_2' => $partnerCompany->extension_2
                ]);

            }

        }

    }
}
