<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AccessTemplateHeader extends Model
{
    protected $table = 'access_template_headers';

    public function company()
    {
        return $this->hasOne("App\\Models\\PartnerCompany", "partner_id", "company_id");
    }

    public function details()
    {
        return $this->hasMany('App\Models\AccessTemplateDetail','header_id','id');
    }

	public static function getAllResourceAccessByGroup($header_id){
		$result = DB::table('access_template_details')
				->join('resources','resources.id','=','access_template_details.resource_id')
				->join('resource_group_accesses','resource_group_accesses.id','=','resources.resource_group_access_id')
				->select('resource_group_accesses.id','resource_group_accesses.name')
				->where('header_id', $header_id)
				->get();

		return $result;

	}
}
