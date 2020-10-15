<?php

namespace App\Models;

use App\Contracts\Departments\DepartmentListService;
use App\Models\UserType;
use App\Traits\ActiveTrait;
use App\Traits\NoDashPhoneTrait;
use App\Traits\SavePhoneWithDashTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class User extends Model
{
	use ActiveTrait, HasApiTokens, NoDashPhoneTrait, SavePhoneWithDashTrait;
	
	protected $table = 'users';
	protected $appends = ['full_name', 'email'];
	protected $dates = ['last_activity', 'created_at', 'updated_at'];
	protected $guarded = [];
	protected $hidden = [
        'password', 'remember_token'
	];
	

	public function apiKeys()
	{
		return $this->hasMany('App\Models\ApiKey');
	}

    public function department()
    {
       return $this->hasOne('App\Models\UserType','id','user_type_id');
    }

    public function departments()
    {
       return $this->hasMany('App\Models\UserTypeReference','user_id','id');
    }

    public function companies()
    {
       return $this->hasMany('App\Models\UserCompany','user_id','id');
    }

	public static function getUserPerProduct($product_id,$company_id = -1){
		if($company_id == -1){
			$cmd = "select distinct u.id, concat(u.first_name,' ',u.last_name) name,ut.description as department from users u
	                inner join user_type_product_accesses utpa on FIND_IN_SET(utpa.user_type_id, u.user_type_id) > 0
	                inner join user_types ut on u.user_type_id = ut.id 
	                where u.status='A' and utpa.product_id={$product_id}     
	                order by name";			
        }else{
			$cmd = "select distinct u.id, concat(u.first_name,' ',u.last_name) name,ut.description as department from users u
	                inner join user_type_product_accesses utpa on FIND_IN_SET(utpa.user_type_id, u.user_type_id) > 0
	                inner join user_types ut on u.user_type_id = ut.id 
	                where u.status='A' and utpa.product_id={$product_id} and u.company_id = {$company_id}   
	                order by name";        	
        }

		$result = DB::select(DB::raw($cmd));
		return $result;

	}

	public static function getDepartmentsByID($id){
     	$ids = explode(",",$id);
		$result = DB::table('user_types')->select('*')
			->where('status', 'A')
				->whereIn('id', $ids)
				->orderBy('description')
				->get();
		
		return $result;

	}


	public static function getAllUsers($company_id=""){
		//DB::enableQueryLog();
		if($company_id!=""){
			$result = DB::table('users')->distinct()->select('users.*',DB::raw("(CASE WHEN users.status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated' END) AS status_text"),DB::raw("IFNULL(partner_companies.company_name,'No Company') AS company"))
				->leftJoin('partner_companies','partner_companies.partner_id','=','users.company_id',' AND users.is_original_partner=1')
				->leftJoin('user_types', function($query) {
				    $query->whereRaw("find_in_set(user_types.id,users.user_type_id)");
				 })
				->whereIn('users.status', array('A','I','T'))
				->where('user_types.create_by','<>','SYSTEM')
				->where('users.company_id', $company_id)
				->get();
		} else {
			$result = DB::table('users')->distinct()->select('users.*',DB::raw("(CASE WHEN users.status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated'  END) AS status_text"),DB::raw("IFNULL(partner_companies.company_name,'No Company') AS company"))
				->whereIn('users.status', array('A','I','T'))
				->leftJoin('user_types', function($query) {
				    $query->whereRaw("find_in_set(user_types.id,users.user_type_id)");
				})
				->leftJoin('partner_companies','partner_companies.partner_id','=','users.company_id',' AND users.is_original_partner=1')
				->where('user_types.create_by','<>','SYSTEM')
				->get();
		}
		//dd(DB::getQueryLog());
		return $result;

	}


	public static function getUsersByDepartment($user_type_id=-1,$company_id=""){

		$cmd = "SELECT users.*, CASE WHEN status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated' END AS status_text
				,IFNULL(partner_companies.company_name,'No Company') AS company
				FROM users 
				INNER JOIN user_type_references utr ON users.id = utr.user_id 
				LEFT JOIN partner_companies ON partner_companies.partner_id = users.company_id
				WHERE users.status IN ('A','I','T')";
		if ($user_type_id != "-1"){
			$cmd .= " AND (";
			$ids = explode(",",$user_type_id);
			foreach ($ids as $id){
				// $cmd .= " FIND_IN_SET({$id},user_type_id) > 0 OR";
				$cmd .= " utr.user_type_id = {$id} OR";
			}
			$cmd = substr($cmd, 0, strlen($cmd)-2);  
			$cmd .= ")";
		}
		if ($company_id != "" && $company_id > -1){
			$cmd.= " AND users.company_id ={$company_id}";
		}

		$result = DB::select(DB::raw($cmd));
		return $result;

	}

	public static function advancedSearchByDepartments($user_type_id=-1,$company_id="",$is_sys_us=-1){

		$cmd = "SELECT DISTINCT users.*, CASE WHEN users.status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated' END AS status_text
				,IFNULL(partner_companies.company_name,'No Company') AS company
				FROM users 
				INNER JOIN user_type_references utr ON users.id = utr.user_id 
				LEFT JOIN partner_companies ON partner_companies.partner_id = users.company_id
				LEFT JOIN user_types ON user_types.id = utr.user_type_id
				WHERE users.status IN ('A','I','T')";
		if ($user_type_id != "-1"){
			$cmd .= " AND (";
			$ids = explode(",",$user_type_id);
			foreach ($ids as $id){
				// $cmd .= " FIND_IN_SET({$id},user_type_id) > 0 OR";	
				$cmd .= " utr.user_type_id = {$id} OR";
			}
			$cmd = substr($cmd, 0, strlen($cmd)-2);  
			$cmd .= ")";
		}
		if ($company_id != "" && $company_id > -1){
			$cmd.= " AND users.company_id ={$company_id}";
		}
		if ($is_sys_us == "1") {
			$cmd.= " AND user_types.create_by <> 'SYSTEM'";
		} else {
			$cmd.= " AND user_types.create_by = 'SYSTEM'";
		}
		$result = DB::select(DB::raw($cmd));
		return $result;

	}

	public static function getAllSystemUsers($company_id=""){
		//DB::enableQueryLog();
		if($company_id!=""){
			$result = DB::table('users')->select('users.*',DB::raw("(CASE WHEN users.status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated' END) AS status_text"),DB::raw("IFNULL(partner_companies.company_name,'No Company') AS company"))
				->leftJoin('partner_companies','partner_companies.partner_id','=','users.company_id',' AND users.is_original_partner=1')
				->leftJoin('user_types', function($query) {
				    $query->whereRaw("find_in_set(user_types.id,users.user_type_id)");
				 })
				->whereIn('users.status', array('A','I','T'))
				->where('user_types.create_by','SYSTEM')
				->where('users.company_id', $company_id)
				->get();
		} else {
			$result = DB::table('users')->distinct()->select('users.*',DB::raw("(CASE WHEN users.status = 'A' THEN 'Active' WHEN users.status = 'I' THEN 'Inactive' ELSE 'Terminated' END) AS status_text"),DB::raw("IFNULL(partner_companies.company_name,'No Company') AS company"))
				->whereIn('users.status', array('A','I','T'))
				->leftJoin('user_types', function($query) {
				    $query->whereRaw("find_in_set(user_types.id,users.user_type_id)");
				})
				->leftJoin('partner_companies','partner_companies.partner_id','=','users.company_id',' AND users.is_original_partner=1')
				->where('user_types.create_by','SYSTEM')
				->get();
		}
		//dd(DB::getQueryLog());
		return $result;

	}

	/** 
	 * Relationships 
	 */
	public function analytics()
	{
		return $this->hasMany('App\Models\Analytics');
	}
	
	public function assignedTickets()
	{
		return $this->hasMany('App\Models\TicketHeader', 'assignee', 'id');
	}
	
	public function partner()
	{
		return $this->belongsTo('App\Models\Partner', 'reference_id');
	}

	public function partnerCompany()
    {
        return $this->belongsTo('App\Models\PartnerCompany', 'company_id', 'partner_id');
	}

	public function requestedTickets()
	{
		return $this->hasMany('App\Models\TicketHeader', 'requester_id', 'id');
	}

	public function tickets()
	{
		return $this->hasMany('App\Models\TicketHeader', 'create_by', 'username');
	}

	public function ticketAttachments()
	{
		return $this->belongsToMany('App\Models\TicketHeader', 'ticket_attachment',
			'user_id', 'ticket_header_id')->withTimestamps();
	}

	public function ticketsCC()
	{
		return $this->belongsToMany('App\Models\TicketHeader', 'ticket_cc',
			'user_id', 'ticket_header_id')->withTimestamps();
	}

	public function ticketDetailAttachments()
	{
		return $this->belongsToMany('App\Models\TicketHeader', 'ticket_attachment',
			'user_id', 'ticket_header_id')->withTimestamps();
	}

	public function requests()
    {
        return $this->hasMany(ChatFriendRequest::class, 'recipient');
	}

	public function userTypes()
    {
        return $this->belongsToMany(
            UserType::class, 
            'user_type_references',
			'user_id',
			'user_type_id');
    }
	
	/** 
	 * Scopes  
	 */
	public function scopeWhereCompany($query, $companyId)
    {
        if (is_null($companyId) || $companyId == -1) {
			return $query;
		}

        return $query->where('company_id', $companyId);
	}
	
    public function scopeWhereUserType($query, $userTypeId)
	{
		return $query->whereRaw("FIND_IN_SET({$userTypeId}, user_type_id) <> 0");
	}

	public function scopeWhereUserTypeIsNot($query, $userTypeId)
	{
		return $query->whereRaw("FIND_IN_SET({$userTypeId}, user_type_id) = 0");
	}

	public function scopeWhereUserTypeIn($query, $userTypeIds)
	{
		return $query->where(function($query) use ($userTypeIds) {
			foreach ($userTypeIds as $userTypeId) {
				$query->orWhereRaw("FIND_IN_SET('{$userTypeId}', user_type_id) <> 0");
			}
		});
	}

	/** 
	 * Accessors 
	 */
	public function getFullNameAttribute()
	{
		$firstName = ucfirst($this->first_name);
		$lastName = ucfirst($this->last_name);

		return "{$firstName} {$lastName}";
	}

	public function getDepartmentNamesAttribute()
	{
		if ($this->relationLoaded('department') && $this->department !== null) {
			return optional($this->department)->description;
		}

        $departments = UserType::isActive()
            ->orderBy('description')
			->find(explode(',', $this->user_type_id));
			
		return implode(', ', $departments->pluck('description')->all());
	}

    public static function getOnlineUsers($company_id = -1)
    {
        $addQuery = $company_id == -1 ? "" : " and p.id = ".$company_id;
        $cmd = "select p.id,pc.company_name from partners p inner join partner_companies pc on pc.partner_id = p.id where p.partner_type_id = 7 ".$addQuery." order by pc.company_name";
        $companies = DB::select(DB::raw($cmd));
        foreach ($companies as $c) {
            $cmd = "select * from user_types where company_id = {$c->id} and status = 'A' order by description";
            $departments = DB::select(DB::raw($cmd));
            $totalCount = 0;
            foreach ($departments as $d) {
                $cmd = "select * from users where CONCAT(user_type_id,',') like '%{$d->id},%' and is_online = 1";
                $users = DB::select(DB::raw($cmd));
                $userCount = 0;
                foreach ($users as $u) {
                	$userCount++;
                	$totalCount++;
                }
                $d->users = $users;
                $d->userCount =  $userCount;
            }
            $c->departments = $departments;
            $c->totalCount = $totalCount;
        }
        return $companies;

	}
	
	public function getMobileNumberAttribute($value)
	{
        return $this->country == 'China' ? str_replace('-','',$value) ?? str_replace('-','',old('mobile_number')) : $value ?? old('mobile_number');
	}

	public function getEnhancedIsOnlineAttribute()
	{
		if ($this->last_activity->diffInMinutes(Carbon::now()) <= 20) {
			if ($this->is_online == 1) {
                return true;
            }
		}
		
		return false;
	}

	public function getEmailAttribute()
	{
		$email_address = isset($this->email_address) ? $this->email_address : "No Email";
	
		return "{$email_address}";
	}

	public function scopeAnalyticsUsers($query)
	{
		return $query->where(function($query) {
			$query->where('is_original_partner', 0)
				->orwhereRaw("FIND_IN_SET('4', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('5', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('6', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('8', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('11', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('13', user_type_id) <> 0");
		});
	}

	public function scopePartner($query)
	{
		return $query->where(function($query) {
			$query->whereRaw("FIND_IN_SET('4', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('5', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('11', user_type_id) <> 0");
		});
	}

	public function scopeAgent($query)
	{
		return $query->where(function($query) {
			$query->whereRaw("FIND_IN_SET('6', user_type_id) <> 0")
				->orWhereRaw("FIND_IN_SET('13', user_type_id) <> 0");
		});
	}

	public function scopeEmployee($query)
	{
		return $query->where('is_original_partner', '0');
	}

	public function scopeMerchant($query) {
		return $query->whereRaw("FIND_IN_SET('8', user_type_id) <> 0");
	}

	public function scopeIsInternal($query)
    {
        return $query->where(function($q) {
			$q->where('username','like', 'U%')
			  ->orWhere('username','like', 'SU%')
			  ->orWhere('username','like', 'C%')
			  ->orWhere('username','like', 'admin');
		});
	}

	public function scopeExcludeSelf($query)
	{
		return $query->where('id', '!=', auth()->user()->id);
	}

	public function scopeInternalUsers($query)
	{
		return $query->where(function($q){
			$q->where('username', 'LIKE', 'U%')
				->where('company_id', auth()->user()->company_id);
			// $q = User::whereCompany(auth()->user()->company_id)->get();
		});
	}

	public function scopeNonInternalUsers($query)
	{
		return $query->where('username', 'NOT LIKE', 'U%');
	}
	
	public function scopeIncludeSuperAdminOwner($query, $adminOwner)
	{
		return $query->orWhereIn('user_type_id', $adminOwner);
	}

	public function scopeIncludeContact($query, $contacts)
	{
		return $query->orWhereIn('id', $contacts);

	}

	public function scopePartnerAccess($query, $partnerAccess)
	{
		return $query->orWhereIn('reference_id', $partnerAccess);
	}

	public function scopeIncludeChatSupport($query, $chatSupports)
	{
		return $query->whereIn('user_type_id', $chatSupports);
	}

	public function scopeIncludeCompany($query)
    {
        return $query->orWhere(function($q) {
			$q->where('company_id', auth()->user()->company_id)
				->where('username', 'LIKE', 'C%');
			// ->whereIn('user_type_id', [11]);
		});
	}

}
