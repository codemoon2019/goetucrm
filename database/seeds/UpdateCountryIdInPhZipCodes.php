<?php

use Illuminate\Database\Seeder;
use App\Models\PhZipCode;

class UpdateCountryIdInPhZipCodes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhZipCode::where('country_id',3)
            ->update(['country_id' => 2]);
    }
}
