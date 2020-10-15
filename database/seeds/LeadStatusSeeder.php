<?php

use App\Models\LeadStatus;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $system = new LeadStatus;
        $system->name = 'Open';
        $system->description = 'Open';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->update_by = 'Seeder';
        $system->save();

        $system = new LeadStatus;
        $system->name = 'Qualified';
        $system->description = 'Qualified';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->update_by = 'Seeder';
        $system->save();

        $system = new LeadStatus;
        $system->name = 'Contacted';
        $system->description = 'Contacted';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->update_by = 'Seeder';
        $system->save();

        $system = new LeadStatus;
        $system->name = 'Unqualified';
        $system->description = 'Unqualified';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->update_by = 'Seeder';
        $system->save();




    }
}
