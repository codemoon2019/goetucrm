<?php

use App\Models\PartnerSystem;
use Illuminate\Database\Seeder;

class PartnerSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $system = new PartnerSystem;
        $system->name = 'Synergy';
        $system->mid_format = '0000000000000000';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PartnerSystem;
        $system->name = 'Card Connect';
        $system->mid_format = '000000000000';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PartnerSystem;
        $system->name = 'Legacy';
        $system->mid_format = '0000000000';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PartnerSystem;
        $system->name = 'Go3 Solutions';
        $system->mid_format = '0000';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

    }
}
