<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserType extends Model
{
    protected $table = 'user_types';
    protected $guarded = [];

    public static function partner_type_access($user_type_id)
    {
        $user_type_id = explode(',', $user_type_id);
        $access = DB::table('user_types')
            ->whereIn('id', $user_type_id)
            ->get();

        return $access;
    }

    /**
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->where('status', 'A');
    }

    public function scopeIsSystem($query)
    {
        return $query->where('create_by', '=', 'SYSTEM');
    }

    public function scopeIsNonSystem($query)
    {
        return $query->where('create_by', '<>', 'SYSTEM');
    }

    public function scopeWhereCompany($query, $companyId)
    {
        if (is_null($companyId) || $companyId == -1)
            return $query;

        return $query->where('company_id', $companyId);
    }

    public function scopeWhereDepartmentIn($query, $departmentIds)
    {
        if ($departmentIds == -1) {
            return $query;
        }

        return $query->whereIn('id', explode(',', $departmentIds));
    }

    /** 
     * Relationships  
     */

    public function ticketHeaders()
    {
        return $this->hasMany('App\Models\TicketHeader', 'department', 'id');
    }
    
    public function users()
    {
        return $this->belongsToMany(
            User::class, 
            'user_type_references',
            'user_type_id',
            'user_id');
    }
    
    public function parent()
    {
        return $this->hasOne("App\\Models\\UserType", "id", "parent_id");
    }

    public function partnerCompany()
    {
        return $this->belongsTo('App\Models\PartnerCompany', 'company_id', 'partner_id');
    }

    public function company()
    {
        return $this->hasOne("App\\Models\\Partner", "id", "company_id");
    }

    public function division()
    {
        return $this->hasOne("App\\Models\\Division", "id", "division_id");
    }
    public function resources()
    {
        return $this->belongsToMany('App\Models\Resource', 'user_templates', 
            'user_type_id', 'resource_id');
    }

    public function departmentHead()
    {
        return $this->belongsTo('App\\Models\\User', 'head_id', 'id');
    }

    public static function getAllCompanyWithDepartment(){
        return DB::select(DB::raw("SELECT ut.company_id,pc.company_name FROM user_types ut left join partner_companies pc on pc.partner_id = ut.company_id where ut.status = 'A' group by ut.company_id,pc.company_name"));

    }
}
