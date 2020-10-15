<?php

namespace App\Models\Drafts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

use App\Models\Partner;
use App\Traits\NoDashPhoneTrait;
use App\Traits\SavePhoneWithDashTrait;

class DraftPartner extends Model
{
    use SoftDeletes, NoDashPhoneTrait, SavePhoneWithDashTrait;

    protected $table = 'draft_partners';

    protected $dates = ['deleted_at'];

    public function partnerType()
    {
        return $this->hasOne('App\Models\PartnerType', 'id', 'partner_type_id');
    }
    
    public function draftPartnerContacts()
    {
        return $this->hasMany("App\Models\Drafts\DraftPartnerContact", "draft_partner_id", "id");
    }

    public function draftLeadComment()
    {
        return $this->hasOne("App\Models\Drafts\DraftLeadComment", "draft_partner_id", "id");
    }

    public function draftPartnerLanguage()
    {
        return $this->hasMany("App\Models\Drafts\DraftPartnerLanguage", "draft_partner_id", "id");
    }

    public function draftPartnerAttachments()
    {
        return $this->hasMany("App\Models\Drafts\DraftPartnerAttachment", "draft_partner_id", "id");
    }

    public function getFullAddressAttribute()
	{
        return "{$this->business_address1} {$this->business_city} {$this->zip} {$this->business_state} {$this->business_country}";
    }
    
    public static function get_draft_partners($id = -1, $partner_type_id = -1, $partner_id = -1, $product_access = -1, $status = "", $sort_by_field = "", $filter = "",$get_upline = true)
    {
        $cmd = "SELECT DISTINCT   
            pt.description AS partner_type,
            IFNULL(dp.dba, '') dba,
            dp.id AS partner_id,
            dp.partner_type_id AS partner_type_id,
            dp.created_at,
            IFNULL(dp.company_name, '') AS company_name,
            IFNULL(dp.partner_email, dpc.contact_email) AS email,
            IFNULL(dp.phone1, dpc.other_number) phone1, 
            IFNULL(dp.phone2, dpc.other_number_2) phone2, 
            IFNULL(dp.business_state, dpc.contact_state) state,
            IFNULL(dp.business_country,dpc.contact_country) AS country_name,
            c.country_calling_code as country_code,
            CASE 
                WHEN dp.partner_type_id IN (1,2) THEN 'partner' 
                WHEN dp.partner_type_id IN (4,5) THEN 'partner'
                WHEN dp.partner_type_id IN (3) THEN 'merchant' 
                WHEN dp.partner_type_id IN (6) THEN 'lead' 
                WHEN dp.partner_type_id IN (8) THEN 'prospects' 
                ELSE 'partner'
            END AS 'controller',
            dpc.first_name,
            dpc.last_name,
            IFNULL(is_verified_email,0) is_verified_email,
            IFNULL(is_verified_mobile,0) is_verified_mobile,
            IFNULL(dp.merchant_id,'') merchant_mid,
            IFNULL(dp.website,'') merchant_url,
            IFNULL(dp.credit_card_reference_id,'') credit_card_reference_id,
            IFNULL(dp.tax_id_number,'') federal_tax_id,
            IFNULL(dp.merchant_processor,'') merchant_processor,
            IFNULL(dp.bank_name,'') bank_name,
            IFNULL(dp.bank_routing_no,'') bank_routing_no,
            IFNULL(dp.partner_id_reference,'') partner_id_reference,
            CASE
                WHEN dp.is_stored_to_partners = 0 THEN 'D'
                ELSE 'A'
            END AS status,dp.parent_id
        FROM draft_partners dp
        INNER JOIN partner_types pt ON pt.id = dp.partner_type_id
        LEFT JOIN countries c ON dp.business_country = c.name 
        LEFT JOIN draft_partner_contacts dpc ON 
            dpc.draft_partner_id = dp.id AND 
            dpc.is_original_contact = 1
        WHERE dp.is_stored_to_partners = 0
        AND dp.deleted_at IS NULL";

        $cmd .= $filter;
                
        if ($id != -1) {
            $cmd .= " AND dp.parent_id IN({$id})";
        }
        if ($partner_type_id != -1) {
            $cmd .= " AND dp.partner_type_id IN({$partner_type_id})";
        }
        $cmd .= $filter;

        if ($sort_by_field != "") {
            $cmd .= " order by " . $sort_by_field;
        } else {
            $cmd .= " order by first_name,last_name";
        }

        $records = DB::raw($cmd);
        $rs = DB::select($records);
        if($get_upline){
            $results = array();
            foreach ($rs as $result) {
                $upline_partner_access = Partner::get_upline_partners_access1($result->parent_id) . ',' . $result->parent_id;
                if ($upline_partner_access != "") {
                    $result->upline_partners = Partner::get_upline_partner_info($upline_partner_access, true);
                } else {
                    $result->upline_partners = array();
                }

                $results[] = $result;
            }
            return $results;            
        }else{
            return $rs;
        }

    }

    public static function get_draft_leads_prospects($parent_id = -1, $partner_type_id = -1, $interested_products = -1)
    {
        $records = "SELECT DISTINCT dp.*, 
            dp.id AS partner_id, 
            dp.merchant_id AS merchant_id, 
            dp.merchant_processor AS processor,
            IFNULL(dp.company_name,'Admin') AS partner, 
            CASE 
                WHEN dp.parent_id = -1 THEN 'Unassigned' 
            END AS partner1, 
            concat(u.first_name, ' ', u.last_name) AS lead_source, 
            concat(dpc.first_name, ' ', dpc.last_name) AS contact_person, 
            dpc.mobile_number, 
            dpc.contact_email, 
            CASE
                WHEN dp.is_stored_to_partners = 0 THEN 'D'
                ELSE 'A'
            END AS partner_status, 
            dp.interested_products, 
            c.country_calling_code as company_country_code ,
            c.country_calling_code as country_code ,
            pt.name AS partner_type, 
            dp.id AS lead_id
            FROM draft_partners dp
            LEFT JOIN partners pp 
                ON pp.id = dp.parent_id
            LEFT JOIN draft_partner_contacts dpc
                ON dpc.draft_partner_id = dp.id 
                AND dpc.is_original_contact = 1
            INNER JOIN users u 
                ON u.username = dp.create_by
            INNER JOIN partner_types pt 
                ON pt.id = dp.partner_type_id
            LEFT JOIN countries c ON dp.business_country = c.name 
            WHERE dp.is_stored_to_partners = 0 
            AND dp.deleted_at IS NULL  ";

        if ($parent_id > 0) {
            $records .= DB::raw(" AND dp.parent_id IN(" . $parent_id . ")");
        }
        if ($partner_type_id > 0) {
            $records .= DB::raw(" AND dp.partner_type_id IN(" . $partner_type_id . ")");
        }
        if ($interested_products > 0) {
            $interested_products = explode(',', $interested_products);
            foreach ($interested_products as $ip) {
                $records .= DB::raw(" AND FIND_IN_SET(" . $ip . ",dp.interested_products) > 0");
            }
        }
        
        $records = DB::select(DB::raw($records));

        $results = array();
        foreach ($records as $r) {
            if ($r->interested_products !== "") {
                $interested_products = explode(',', $r->interested_products);
                $customs = DB::table('products')
                    ->select('name')
                    ->whereIn('id', $interested_products)
                    ->get();
                $r->interested_products = $customs;
            } else {
                $r->interested_products = array();
            }
            $upline_partner_access = Partner::get_upline_partners_access1($r->parent_id) . ',' . $r->parent_id;

            if ($upline_partner_access != "") {
                $upa_response = Partner::get_upline_partner_info($upline_partner_access, true);
                $r->upline_partners = $upa_response;
            } else {
                $r->upline_partners = array();
            }
            $results[] = $r;
        }

        return $results;
    }

}
