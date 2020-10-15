<?php

use Illuminate\Database\Seeder;

class UpdateDraftEdittoResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('resources')
            ->where('id', 316)
            ->update(['deleted' => 1]);

        DB::table('resource_group_accesses')
            ->where('id', 180)
            ->update(['status' => 'I']);
    }
}
