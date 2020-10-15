<?php

use Illuminate\Database\Seeder;
use App\Models\Document;


class AddOtherFormsToDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Document::create([
            'name' => 'OTHERS',
            'code' => 'others',
            'create_by' => 'seeder',
            'sequence' => 0,
            'status' => 'A'
        ]);
    }
}
