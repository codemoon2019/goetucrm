<?php

use Illuminate\Database\Seeder;
use App\Models\Training;

class TrainingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $trainings = DB::connection('mysql_old')->table('trainings')->get();
        if(isset($trainings))
        {
            foreach($trainings as $training){
                Training::create([
                    'name' => $training->name,
                    'description' => $training->description,
                    'product_id' => $training->product_id,
                    'status' => $training->status,
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder'
                ]);
            }
        }


    }
}
