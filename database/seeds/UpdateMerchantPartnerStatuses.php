<?php

use App\Models\MerchantStatus;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class UpdateMerchantPartnerStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Partner::where('partner_type_id', 3)
            ->where('status', 'A')
            ->update([
                'merchant_status_id' => MerchantStatus::LIVE_ID,
                'update_by' => 'admin'
            ]);

        Partner::where('partner_type_id', 3)
            ->where('status', 'P')
            ->update([
                'merchant_status_id' => MerchantStatus::BOARDING_ID,
                'update_by' => 'admin'
            ]);

        Partner::where('partner_type_id', 3)
            ->where('status', 'C')
            ->update([
                'merchant_status_id' => MerchantStatus::FOR_APPROVAL_ID,
                'update_by' => 'admin'
            ]);
    }
}
