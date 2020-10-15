<?php

use App\Models\Resource;
use Illuminate\Database\Seeder;

class BannerAccessDeleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Resource::create([
            'resource' => 'admin/banners/edit',
            'description' => 'Delete',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'deleted' => 0,
            'resource_group_id' => 24,
            'resource_group_access_id' => 178,
            'created_at' => '2018-10-02 11:00:00',
            'updated_at' => '2018-10-02 11:00:00',
        ]);
    }
}
