<?php

use App\Models\SubTaskDetail;
use Illuminate\Database\Seeder;

class UpdateSubtaskDetailStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubTaskDetail::where('status', '')
            ->orWhere('status', 'A')
            ->orWhere('status', null)
            ->update(['status' => 'T']);
    }
}
