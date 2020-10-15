<?php

namespace App\Models;

use App\Traits\NoDashPhoneTrait;
use App\Traits\SavePhoneWithDashTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class PartnerContact extends Model
{
    use NoDashPhoneTrait, SavePhoneWithDashTrait;

    protected $table = 'partner_contacts';

    protected $guarded = [];

    public function user()
    {
       return $this->hasOne('App\Models\User','id','reference_id');
    }

    public function partner()
    {
       return $this->hasOne('App\Models\Partner','id','partner_id');
    }

    public static function get_contact_info($id, $partner_id, $partner_access=-1)
    {
        $cmd="SELECT c.id, partner_id, c.first_name, c.last_name, c.middle_name, 
            c.email, c.mobile_number, c.other_number, c.position, c.address1, 
            c.address2, c.city, c.state, c.zip, c.country, c.updated_at, c.update_by, 
            c.extension, c.company_name, c.create_by, c.created_at, c.is_original_contact, 
            c.ssn, c.website, c.other_number_2, c.fax, c.email2, DATE_FORMAT(c.dob,'%m/%d/%Y') dob, 
            c.country_code, c.ownership_percentage,c.mobile_number_2 
            ,ifnull(is_verified_email,0) is_verified_email ,ifnull(is_verified_mobile,0) is_verified_mobile
            ,DATE_FORMAT(c.business_acquired_date,'%m-%d-%Y') business_acquired_date,c.issued_id
            ,DATE_FORMAT( c.id_exp_date,'%m-%d-%Y') id_exp_date
            ,SUBSTRING(c.mobile_number, 2, LENGTH(c.mobile_number)) AS nd_mobile_number
            ,SUBSTRING(c.mobile_number_2, 2, LENGTH(c.mobile_number_2)) AS nd_mobile_number_2
            ,SUBSTRING(c.other_number, 2, LENGTH(c.other_number)) AS nd_other_number
            ,SUBSTRING(c.other_number_2, 2, LENGTH(c.other_number_2)) AS nd_other_number_2
            from partner_contacts c
            inner join partners p on p.id = c.partner_id
            LEFT JOIN users u on u.reference_id = p.id 
            where c.id={$id} and p.id={$partner_id}
            ";
        if ($partner_access != -1){
            $cmd .= " and p.parent_id in ({$partner_access})";
        }
        $results = DB::select(DB::raw($cmd));
        return $results[0];
    }
}

	