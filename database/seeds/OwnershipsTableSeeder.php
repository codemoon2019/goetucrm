<?php

use Illuminate\Database\Seeder;
use App\Models\Ownership;

class OwnershipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ownership::create([
            'code' => 'ASSOC',
            'name' => 'Association',
            'description' => 'Association',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'CORP',
            'name' => 'Corporation',
            'description' => 'Corporation',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'ESTATE',
            'name' => 'Estate/Trust',
            'description' => 'Estate/Trust',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'GOVT',
            'name' => 'Government',
            'description' => 'Government',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'INDIVSOLE',
            'name' => 'Individual / Sole Proprietor',
            'description' => 'Individual / Sole Proprietor',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'LLC',
            'name' => 'LLC',
            'description' => 'LLC',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'LP',
            'name' => 'LP',
            'description' => 'LP',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'NONPRFT',
            'name' => 'Non-Profit Org',
            'description' => 'Non-Profit Org',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);


        Ownership::create([
            'code' => 'OTHER',
            'name' => 'Other',
            'description' => 'Other',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'PRTNRSHP',
            'name' => 'Partnership',
            'description' => 'Partnership',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'PRTNRGEN',
            'name' => 'Partnership (General)',
            'description' => 'Partnership (General)',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'PRTNRLIM',
            'name' => 'Partnership (Limited)',
            'description' => 'Partnership (Limited)',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'PRIVCORP',
            'name' => 'Private Corporation',
            'description' => 'Private Corporation',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'PUBCORP',
            'name' => 'Public Corporation',
            'description' => 'Public Corporation',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'SCHOOL',
            'name' => 'School',
            'description' => 'School',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        Ownership::create([
            'code' => 'SUBSCORP',
            'name' => 'Sub S Corporation',
            'description' => 'Sub S Corporation',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);
        
        Ownership::create([
            'code' => 'TAXEXMPT',
            'name' => 'Tax Exempt',
            'description' => 'Tax Exempt',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

    }
}
