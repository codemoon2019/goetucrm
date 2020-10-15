<?php

use Illuminate\Database\Seeder;
use App\Models\BusinessType;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BusinessTypesTableSeeder extends Seeder
{
    public function run()
    {
        Excel::load("storage/mcc/mcc.xls", function($reader) {
            $sheet = $reader->first();
            $rows = $sheet->all();

            $merchantCategoryGroups = collect(config('mcc'));
            $now = Carbon::now();
            $data = [];
            foreach ($rows as $row) {
                if ($row['mcc_code'] == null) {
                    continue;
                }

                $mcc = $row['mcc_code'];
                $group = $merchantCategoryGroups->first(function($value, $index) use ($mcc) {
                    return $value['start'] <= $mcc && $mcc <= $value['end'];
                });

                $data[] = [
                    'mcc' => $row['mcc_code'],
                    'group' => $group['name'],
                    'description' => $row['program_type'],
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'status' => 'A',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            BusinessType::truncate();
            BusinessType::insert($data);
        });
    }
}
