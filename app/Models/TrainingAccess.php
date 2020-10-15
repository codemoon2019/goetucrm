<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TrainingAccess extends Model
{
    protected $table = 'training_accesses';

    public static function get_training_access($id)
    {
        $result = DB::select(DB::raw("SELECT  t.name as training_name,tm.name as module_name,
				pt.name as partner_type,ta.* from training_accesses ta
				inner join partner_types pt on ta.partner_type_id = pt.id
				inner join trainings t on t.id = ta.training_id
				inner join training_modules tm on t.id = tm.training_id and ta.module_code = tm.module_code
				where ta.partner_id = {$id} 
				order by pt.name,t.name,tm.name
        "));

        return $result;
    }

    public static function get_available_training_modules($product_id)
    {
        $result = DB::select(DB::raw("SELECT t.id,t.name as training_name,tm.name as module_name,tm.module_code from trainings t
				inner join training_modules tm on tm.training_id = t.id
				where t.product_id in ({$product_id})
				order by t.name,tm.name
        "));

        return $result;
    }
}
