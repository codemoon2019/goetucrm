<?php

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemSetting::truncate();

        SystemSetting::create([
            'name' => 'Lead Approval Expiration (Hours)',
            'set_code' => 'incoming_lead_approval',
            'set_value' => '24'
        ]);

        SystemSetting::create([
            'name' => 'Additional Days for Due Date',
            'set_code' => 'due_date_add_days',
            'set_value' => '7'
        ]);

        SystemSetting::create([
            'name' => 'Chat On - Off',
            'set_code' => 'chat_on_off',
            'set_value' => '1'
        ]);

        SystemSetting::create([
            'name' => 'Ticket Master',
            'set_code' => 'ticket_master',
            'set_value' => 'veronr@goetu.com'
        ]);
    }
}
