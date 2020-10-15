<?php

use Illuminate\Database\Seeder;
use App\Models\Document;

class AddFormsToDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Document::create([
            'name' => 'AGENT FORM',
            'code' => 'agent_form',
            'create_by' => 'seeder',
            'sequence' => 5,
            'status' => 'A'
        ]);
        
        Document::create([
            'name' => 'APPLICATION FORM',
            'code' => 'app_form',
            'create_by' => 'seeder',
            'sequence' => 6,
            'status' => 'A'
        ]);

    }
}
