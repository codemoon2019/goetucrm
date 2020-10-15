<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Partner;
use App\Models\PartnerType;

class PartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get partners from old database
         */
        $partners = DB::connection('mysql_old')->table('partners')->get();

        if(isset($partners))
        {
            foreach($partners as $partner){
                /**
                 * Create new partners
                 */
                Partner::create([
                    'id' => $partner->id,
                    'create_by' => $partner->create_by,
                    'update_by' => $partner->update_by,
                    'status' => $partner->status,
                    'partner_type_id' => $partner->partner_type_id,
                    'parent_id' => $partner->parent_id,
                    'logo' => $partner->logo,
                    'merchant_mid' => $partner->merchant_mid,
                    'merchant_processor' => $partner->merchant_processor,
                    'agent_partner' => $partner->agent_partner,
                    'partner_id_reference' => $partner->partner_id_reference,
                    'interested_products' => $partner->interested_products,
                    'partner_status' => $partner->partner_status,
                    'original_partner_type_id' => $partner->original_partner_type_id,
                    'original_parent_id' => $partner->original_parent_id,
                    'federal_tax_id' => $partner->federal_tax_id,
                    'credit_card_reference_id' => $partner->credit_card_reference_id,
                    'services_sold' => $partner->services_sold,
                    'merchant_url' => $partner->merchant_url,
                    'authorized_rep' => $partner->authorized_rep,
                    'IATA_no' => $partner->IATA_no,
                    'tax_filing_name' => $partner->tax_filing_name
                ]);
            }
        }
    }
}
