<?php

namespace App\Models;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;
use DB;

class AccessControlList extends Model
{
    protected $table = "resources";
    public $timestamps = false;

	public static function getResourceGroups($user_type_id=-1){

		$cmd ="SELECT distinct resource_groups.* FROM resource_groups ";
        if($user_type_id > 0){
            $cmd .= " inner join resources r on r.resource_group_id = resource_groups.id 
                      inner join user_templates ut on r.id = ut.resource_id ";    
        }
        $cmd .= " WHERE resource_groups.status='A'  ";
        if($user_type_id > 0){
            $cmd .= " and ut.user_type_id in({$user_type_id})";    
        }                                       
        $cmd .= "order by resource_groups.name";
		$result = DB::select(DB::raw($cmd));
		return $result;

	}

	public static function getResourceGroupAccess($resource_group_id,$user_type_id="-1"){
        $cmd ="SELECT * FROM resource_group_accesses WHERE status='A' and resource_group_id={$resource_group_id}";
        if ($user_type_id != "-1"){
            $cmd .= " AND id IN (SELECT distinct r.resource_group_access_id FROM resources r
                    inner join user_templates ut on ut.resource_id = r.id
                    and ut.user_type_id IN ({$user_type_id}) and r.resource_group_id ={$resource_group_id})";
        }
        $cmd .= " order by name";
		$result = DB::select(DB::raw($cmd));
		return $result;

	}

	public static function getResourcesViaResourceGroupAccess($resource_group_access_id){
		

		if (is_array($resource_group_access_id)) {
			$result = Resource::where('deleted', 0)
				->whereIn('resource_group_access_id', $resource_group_access_id)
				->get();
		} else {

			$result = DB::table('resources')->select('*')
				->where('deleted', 0)
				->where('resource_group_access_id', $resource_group_access_id)
				->get();

		}
 
		return $result;

	}

	public static function getAllResourceAccessByGroup($group_id){
		$result = DB::table('user_templates')
				->join('resources','resources.id','=','user_templates.resource_id')
				->join('resource_group_accesses','resource_group_accesses.id','=','resources.resource_group_access_id')
				->select('resource_group_accesses.id','resource_group_accesses.name')
				->where('user_type_id', $group_id)
				->get();

		return $result;

	}


	public static function getAllResources(){
		$result =   DB::table('resources')
             ->join('resource_groups','resources.resource_group_id','=','resource_groups.id')
             ->select('resources.id','resources.description', 'resources.resource','resources.resource_group_access_id','resource_groups.name','resources.resource_group_id')
             ->where('deleted',0)
             ->orderBy('resource_groups.name','resources.description','asc')->get();
		return $result;

	}

}
