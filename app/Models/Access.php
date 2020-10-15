<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

use Illuminate\Support\Facades\Cache;

class Access extends Model
{
    protected $table = "user_templates";
    public $timestamps = false;

    public static function deleteAccessRightsByUserTypeID($user_type_id){
    	 DB::table('user_templates')
            ->where('user_type_id', $user_type_id)->delete();
    }


    public static function getAllUserAccess($user_type_id){
    	 $ids= explode(",",$user_type_id);
    	 $result = DB::table('user_templates')->distinct()
				->join('resources','resources.id','=','user_templates.resource_id')
				->join('resource_groups','resource_groups.id','=','resources.resource_group_id')
				->join('resource_group_accesses','resource_group_accesses.id','=','resources.resource_group_access_id')
				->select('resource_group_accesses.id','resource_groups.name as module_name','resource_group_accesses.name as module_access')
				->whereIn('user_type_id', $ids)
				->orderBy('resource_groups.name','asc')
                ->orderBy('resource_group_accesses.name','asc')->get();
		return $result;
    }


    public static function hasPageAccess($module_name, $module_access, $return_status=false){
        $valid = false;
        //dd($access = session('all_user_access'));
        if (session()->has('all_user_access')) {
            $access = session('all_user_access');
            if (isset($access[$module_name])){
                if(strpos($access[$module_name], $module_access) !== false){
                     $valid=true;
                }
            }
        }
        if ($return_status===true) {
            return $valid;
        } else {
            if (!$valid){
                return redirect('/')->with('failed','You have no access to that page.')->send();   
            }
        }
        
    }

    public static function checkIfProfileExist($table, $field, $value, $id = -1, $include_status = true, $id_prefix ="")
    {
        $value =strtoupper($value);
        $cmd ="SELECT id FROM {$table} WHERE ucase({$field})= '{$value}'";
        if ($id !=-1){
             $cmd.=" AND {$id_prefix}id<>{$id}";
        }
        
        if($include_status)
        {
            $cmd .=" AND status IN ('A','T','I')";
        } 
        $result = DB::select(DB::raw($cmd));
        if (count($result)>0) {
            return true;
        } else { 
            return false;
        }
        
    }

    public static function generateAllUserAccess($user_type_id){
        $accesses = Access::getAllUserAccess($user_type_id);
        $all_user_access = array();
        $mod_name = "";
        $access = "";
        foreach($accesses as $a){
            if ($mod_name==""){$mod_name = $a->module_name;}
            if ($mod_name == $a->module_name)
            {
                $access = $access . $a->module_access . ",";  
            } else {
                $access = substr($access, 0, strlen($access) - 1); 
                $all_user_access = $all_user_access +  array(
                    strtolower($mod_name) => strtolower($access),    
                );
               $access="";
               $mod_name = $a->module_name; 
               $access = $access . $a->module_access . ",";                                                                            
            }
        }

        if (strlen($access)>0){
            $access = substr($access, 0, strlen($access) - 1); 
            $all_user_access = $all_user_access +  
                array(
                    strtolower($mod_name) => strtolower($access),    
                );
        }

        return $all_user_access;
    }

    public static function get_table_field_by_value($table,$selected_field,$search_field,$search_value,$default_value='',$withStatus=false,$parent_id=-1) {    
        $status ="";
        if($withStatus)
        {
            $status = " and status = 'A'";
        }
        
        if($parent_id != -1)   
        {
            $query =  DB::select(DB::raw("select {$selected_field} from {$table} where parent_id = {$parent_id} {$status} and {$search_field} ='{$search_value}'"));
        }   
        else
        {
            $query =  DB::select(DB::raw("select {$selected_field} from {$table} where {$search_field} ='{$search_value}'".$status));
        }   

        if ($query) {
            return $query[0]->$selected_field;
        } else {
            return $default_value;
        }
    }

    public static function getPermissions($user_type_id){
         $ids= explode(",",$user_type_id);
         $result = DB::table('user_templates')->distinct()
                ->join('resources','resources.id','=','user_templates.resource_id')
                ->select('resources.resource')
                ->whereIn('user_type_id', $ids)
                ->orderBy('resources.resource','asc')->pluck('resource')->toArray();
        return $result;
    }
}
