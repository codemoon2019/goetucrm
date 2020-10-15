<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SubTaskTemplateHeader extends Model
{
    protected $table = 'sub_task_template_headers';
    protected $guarded = [];

    const STATUS_ACTIVE = 'A';

    public function details()
    {
        return $this->hasMany('App\Models\SubTaskTemplateDetail','sub_task_id','id');
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }
    public static function api_get_company_task_templates($company_id){
        if($company_id == -1){
            return DB::select(DB::raw("select sh.*,products.name as product_name from sub_task_template_headers sh
                     inner join products on products.id = sh.product_id
                     where sh.status = 'A'"));
        }else{
            return DB::select(DB::raw("select sh.*,products.name as product_name from sub_task_template_headers sh
                     inner join products on products.id = sh.product_id
                     where product_id in(
                    select p2.id from partner_products pp
                    inner join products p on p.id = pp.product_id 
                    inner join products p2 on p.parent_id = p2.id 
                    where pp.partner_id = {$company_id} 
                    group by p2.id) and sh.status = 'A'"));
        }

    }

    
    public function subtaskTemplates()
    {
        return $this->hasMany(SubTaskTemplateDetail::class, 'sub_task_id', 'id');
    }
}
