<?php

use Illuminate\Database\Seeder;
use App\Models\Document;


class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('documents')->delete();

        Document::create([
            'name' => 'VALID ID',
            'code' => 'valid_id',
            'create_by' => 'seeder',
            'sequence' => 3,
            'status' => 'A'
        ]);

        Document::create([
            'name' => 'VOID CHECK',
            'code' => 'void_check',
            'create_by' => 'seeder',
            'sequence' => 4,
            'status' => 'A'
        ]);

        Document::create([
            'name' => 'CORPORATION CERTIFICATE',
            'code' => 'corp_cert',
            'create_by' => 'seeder',
            'sequence' => 1,
            'status' => 'A'
        ]);

        Document::create([
            'name' => 'CORPORATION TAX ID',
            'code' => 'corp_tax_id',
            'create_by' => 'seeder',
            'sequence' => 2,
            'status' => 'A'
        ]);

        // Document::create([
        //     'name' => 'OTHERS',
        //     'code' => 'others',
        //     'create_by' => 'seeder',
        //     'sequence' => 5,
        //     'status' => 'A'
        // ]);


    }
}
