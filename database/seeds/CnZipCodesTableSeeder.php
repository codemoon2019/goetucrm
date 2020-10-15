<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CnZipCode;
use Carbon\Carbon;

class CnZipCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$strJsonFileContents = file_get_contents(storage_path("/zip/cn/zip_code_cn.json"));
		$array = json_decode($strJsonFileContents, true);
		$cnZip = $array['citiesList'];

        $now = Carbon::now();

		foreach ($cnZip as $key) {
            CnZipCode::create([
                'zip_code' => $key['zip'],
                'city' => $key['name'],
                'state_id' => $key['region1'],
                'country_id' => 3,
                'is_primary_city' => 0,
                'is_acceptable_city' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
		}
    }
}
