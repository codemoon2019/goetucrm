<?php

namespace App\Models;

use App\Models\PartnerContact;
use App\Models\PartnerProductAccess;
use App\Models\User;
use App\Traits\ActiveTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Partner extends Model
{
    use ActiveTrait;
    
    const COMPANY_ID = 7;
    const ISO_ID = 4;
    const SUB_ISO_ID = 5;
    const AGENT_ID = 1;
    const SUB_AGENT_ID = 2;
    const MERCHANT_ID = 3;
    const LEAD_ID = 6;
    const PROSPECT_ID = 8;
    const OWNER = "OWNER";

    protected $table = 'partners';

    protected $guarded = [];

    public function partnerCompany()
    {
        return $this->hasOne('App\Models\PartnerCompany');
	}
    

    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == -1)
            return $query;

        return $query->where('company_id', $companyId);
    }

    /**
     * Partner has one partner contact
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partnerContact()
    {
        return $this->hasOne("App\\Models\\PartnerContact", "partner_id", "id");
    }

    public function productOrders()
    {
        return $this->hasMany("App\Models\ProductOrder", "partner_id", "id");
    }

    /**
     * Connected user to a partner using reference_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language()
    {
        return $this->hasMany("App\\Models\\PartnerLanguage", "partner_id", "id");
    }

    public function connectedUser()
    {
        return $this->hasOne("App\\Models\\User", "reference_id", "id");
    }

    public function partnerPaymentInfos()
    {
        return $this->hasMany('App\Models\PartnerPaymentInfo', 'partner_id', 'id');
    }

    public function partner_company()
    {
        return $this->hasOne('App\Models\PartnerCompany', 'partner_id', 'id');
    }

    public function partner_dba()
    {
        return $this->hasOne('App\Models\PartnerDbaAddress', 'partner_id', 'id');
    }

    public function partner_shipping()
    {
        return $this->hasOne('App\Models\PartnerShippingAddress', 'partner_id', 'id');
    }

    public function partner_mailing()
    {
        return $this->hasOne('App\Models\PartnerMailingAddress', 'partner_id', 'id');
    }

    public function partner_billing()
    {
        return $this->hasOne('App\Models\PartnerBillingAddress', 'partner_id', 'id');
    }

    public function partner_contact()
    {
        return PartnerContact::where("partner_id", $this->id)->where("is_original_contact", 1)->first();
    }

    public function partner_contact_other()
    {
        return PartnerContact::where("partner_id", $this->id)->where("is_original_contact", 0)->get();
    }

    public function partner_type()
    {
        return $this->hasOne('App\Models\PartnerType', 'id', 'partner_type_id');
    }

    public function departments()
    {
        return $this->hasMany('App\Models\UserType', 'company_id', 'id')->where('user_types.status', '=', 'A');;
    }

    public function partner_product_access($id)
    {
        return PartnerProductAccess::where("partner_id", $id)->first();
    }

    public function user()
    {
        return User::where("reference_id", $this->id)->where("is_partner", 1)->first();
    }

    public function ticketHeaders()
    {
        return $this->hasMany('App\Models\TicketHeader', 'company_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'username', 'create_by');
    }

    public function partner_mid()
    {
        return $this->hasMany("App\\Models\\PartnerMid", "partner_id", "id");
    }

    public function lead_status()
    {
        return $this->hasOne("App\\Models\\LeadStatus", "id", "lead_status_id");
    }

    public static function get_partners_access($partner_id = -1)
    {
        $id = array();
        $counter = 1;
        $id[0] = $partner_id;
        $original_partner_id = $partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        $records = DB::select(DB::raw("select id,parent_id from partners where parent_id IN ($partner_id)"));
        //print_r($records);die();
        foreach ($records as $r) {
            if ($r->id == $original_partner_id) {
                goto exitquery;
            }
            $id[$counter] = $r->id;
            $temp_id = $temp_id . $r->id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        // print_r($return_data);die();
        return $return_data;
    }

    public static function partner_leads_prospects($parent_id = -1, $partner_type_id = -1, $interested_products = -1)
    {
        // $parent_id = explode(',', $parent_id);
        // $partner_type_id = explode(',', $partner_type_id);

        $records = DB::raw("SELECT DISTINCT pc.*,p.partner_id_reference as merchant_id, p.merchant_processor
        as processor,ifnull(ppc.company_name,'Admin') as partner
        ,case when (p.parent_id = -1 and il.status = 'N') then 'Pending Approval' when p.parent_id = -1 then 'Unassigned' end as partner1
        ,concat(u.first_name, ' ', u.last_name) as lead_source
        ,concat(pcon.first_name, ' ', pcon.last_name) as contact_person
        ,pcon.mobile_number,pcon.email,p.partner_status,p.interested_products
        ,pc.country_code as company_country_code,pcon.country_code as contact_country_code
        ,pt.name as partner_type,p.id as lead_id,ls.name as lead_status
        FROM partners p
        INNER JOIN partner_companies pc ON p.id=pc.partner_id
        INNER JOIN partner_contacts pcon on p.id = pcon.partner_id and pcon.is_original_contact = 1
        LEFT JOIN partners pp on pp.id=p.parent_id
        LEFT JOIN partner_companies ppc ON pp.id=ppc.partner_id
        LEFT JOIN partner_contacts ON partner_contacts.partner_id=pp.id AND partner_contacts.is_original_contact = 1
        INNER JOIN users u on u.username = p.create_by
        INNER JOIN partner_types pt on pt.id = p.partner_type_id
        INNER JOIN lead_statuses ls on ls.id = p.lead_status_id
        LEFT JOIN incoming_leads il on il.partner_id=p.id
        WHERE p.status IN ('A','I') ");

        if ($parent_id > 0) {
            $records .= DB::raw(" AND p.parent_id IN(" . $parent_id . ")");
        }
        if ($partner_type_id > 0) {
            $records .= DB::raw(" AND p.partner_type_id IN(" . $partner_type_id . ")");
        }
        if ($interested_products > 0) {
            $interested_products = explode(',', $interested_products);
            foreach ($interested_products as $ip) {
                $records .= DB::raw(" AND FIND_IN_SET(" . $ip . ",p.interested_products) > 0");
            }
        }

        $records = DB::select($records);

        $results = array();
        foreach ($records as $r) {
            if ($r->interested_products !== "") {
                $interested_products = explode(',', $r->interested_products);
                $customs = DB::table('products')
                    ->select('name')
                    ->whereIn('id', $interested_products)
                    ->get(); //"SELECT name FROM products WHERE id in ({$r['interested_products']})";
                $r->interested_products = $customs;
            } else {
                $r->interested_products = array();
            }
            $upline_partner_access = Partner::get_upline_partners_access($r->lead_id);
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

    public static function get_upline_partners_access($partner_id)
    {
        $id = array();
        $counter = 0;
        $original_partner_id = $partner_id;
        //$id[0]=$partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        $records = DB::select(DB::raw("select id,parent_id from partners where id IN ({$partner_id})"));
        // print_r($records);die();
        foreach ($records as $r) {
            if ($r->parent_id == $original_partner_id) {
                goto exitquery;
            }
            $id[$counter] = $r->parent_id;
            $temp_id = $temp_id . $r->parent_id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        // print_r($return_data);die();
        return $return_data;

    }

    public static function get_upline_partners_access1($partner_id)
    {
        $id = array();
        $counter = 0;
        // $original_partner_id = $partner_id;
        $id[0]=$partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        $records = DB::select(DB::raw("select id,parent_id from partners where id IN ({$partner_id})"));
        // print_r($records);die();
        foreach ($records as $r) {
            if ($r->parent_id == -1) {
                goto exitquery;
            }
            $id[$counter] = $r->parent_id;
            $temp_id = $temp_id . $r->parent_id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        // print_r($return_data);die();
        return $return_data;

    }

    public static function get_merchant_uplines($limit_id,$partner_id)
    {
        $id = array();
        $counter = 0;
        $original_partner_id = $partner_id;
        //$id[0]=$partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        $records = DB::select(DB::raw("select id,parent_id from partners where id IN ({$partner_id})"));
        // print_r($records);die();
        foreach ($records as $r) {
            if ($r->parent_id == $original_partner_id) {
                goto exitquery;
            }
            $id[$counter] = $r->parent_id;
            $temp_id = $temp_id . $r->parent_id . ",";
            if($r->id == $limit_id){
                goto exitquery;
            }

            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        // print_r($return_data);die();
        return $return_data;

    }


    public static function get_upline_partner_info($id, $isFetchAll = false)
    {
        $rs = DB::select(DB::raw("SELECT p.id,pcon.last_name, pcon.first_name, p.partner_id_reference as merchant_id,pc.company_name
        FROM partners p
        LEFT JOIN partner_types pt on p.partner_type_id = pt.id
        LEFT JOIN partner_contacts pcon on p.id=pcon.partner_id  and is_original_contact =1
        LEFT JOIN partner_companies pc on p.id=pc.partner_id
        LEFT JOIN partner_payment_methods ppm on p.id=ppm.partner_id
        WHERE p.status='A' AND p.id IN ($id)
        ORDER BY pt.sequence
        "));

        return $rs;
    }

    public static function get_partner_info($id, $isFetchAll = false, $partner_type_id = -1, $parent_id = -1)
    {
        $records = DB::raw("SELECT p.credit_card_reference_id,p.partner_id_reference,p.parent_id,p.id as partner_id, p.merchant_processor,p.partner_status,p.merchant_mid,p.federal_tax_id
        ,pc.dba, pc.company_name,pc.email, pc.phone1, pc.phone2,pc.state,pc.country as country_name,pc.extension as business_extension
        ,pc.extension_2 as business_extension_2, pc.extension_3 as business_extension_3 
        ,pc.zip, pc.city, p.partner_type_id,pc.address1, pc.address2,pc.mobile_number as company_mobile_number, pc.ownership
        ,pc.country_code as company_country_code,pc.ssn,pc.business_date,pc.website,pc.fax
        ,pcon.last_name, pcon.first_name, pcon.position, p.partner_id_reference as merchant_id
        ,pcon.position, pcon.mobile_number, pcon.other_number as office_number, pcon.other_number_2 as office_number_2
        ,pcon.extension, pcon.email as contact_email, pc.fax, pcon.fax as contact_fax, IF(pcon.country IS NULL or pcon.country = '', 'United States', pcon.country) as country
        ,pcon.address1 as contact_address1,pcon.address2 as contact_address2,pcon.city as contact_city
        ,pcon.state as contact_state,pcon.zip as contact_zip, pcon.middle_name
        ,ppm.bank_name, ppm.account_name, ppm.account_number, ppm.routing_number
        ,pma.country as business_country
        ,pma.address as business_address
        ,pma.address2 as business_address2
        ,pma.city as business_city
        ,pma.state as business_state
        ,pma.zip as business_zip
        ,p.business_type_code as business_type_code
        ,concat(u.first_name, ' ', u.last_name) as partner_source
        ,pt.name as partner_type_description
        ,ifnull(uv.is_verified_email,0) is_verified_email ,ifnull(uv.is_verified_mobile,0) is_verified_mobile
        ,ifnull(pda.country,'') as dba_country
        ,ifnull(pda.address,'') as dba_address
        ,ifnull(pda.address2,'') as dba_address2
        ,ifnull(pda.city,'') as dba_city
        ,ifnull(pda.state,'') as dba_state
        ,ifnull(pda.zip,'') as dba_zip
        ,ifnull(pba.country,'') as billing_country
        ,ifnull(pba.address,'') as billing_address
        ,ifnull(pba.address2,'') as billing_address2
        ,ifnull(pba.city,'') as billing_city
        ,ifnull(pba.state,'') as billing_state
        ,ifnull(pba.zip,'') as billing_zip
        ,ifnull(psa.country,'') as shipping_country
        ,ifnull(psa.address,'') as shipping_address
        ,ifnull(psa.address2,'') as shipping_address2
        ,ifnull(psa.city,'') as shipping_city
        ,ifnull(psa.state,'') as shipping_state
        ,ifnull(psa.zip,'') as shipping_zip
        ,ifnull(pc.business_date,'') as business_date
        ,ifnull(pc.business_name,'') as business_name
        ,ifnull(p.social_security_id,'') as social_security_id
        ,ifnull(p.tax_id_number,'') as tax_id_number
        ,ifnull(p.bank_name,'') as bank_name
        ,ifnull(p.bank_routing_no,'') as bank_routing_no
        ,ifnull(p.bank_dda,'') as bank_dda
        ,ifnull(p.bank_address,'') as bank_address
        ,ifnull(p.email_unpaid_invoice,'') as email_unpaid_invoice
        ,ifnull(p.email_paid_invoice,'') as email_paid_invoice
        ,ifnull(p.email_notifier,'') as email_notifier
        ,ifnull(p.smtp_settings,'') as smtp_settings
        ,ifnull(p.billing_cycle,'') as billing_cycle
        ,ifnull(p.billing_month,'') as billing_month
        ,ifnull(p.billing_day,'') as billing_day
        ,p.services_sold
        ,p.merchant_url
        ,p.authorized_rep
        ,p.IATA_no
        ,p.tax_filing_name
        ,pcon.ownership_percentage
        ,pcon.business_acquired_date
        ,DATE_FORMAT(pcon.dob,'%m/%d/%Y')as dob
        ,pcon.issued_id
        ,DATE_FORMAT(pcon.id_exp_date,'%m/%d/%Y')as id_exp_date
        ,pcon.ssn as sss
        ,ppi.bank_name as setting_bank
        ,ppi.routing_number as setting_route
        ,ppi.bank_account_number as setting_accno
        ,p.status,pt.display_name,p.lead_status_id
        ,SUBSTRING(pc.phone1, 2, LENGTH(pc.phone1)) AS nd_phone1
        ,SUBSTRING(pc.phone2, 2, LENGTH(pc.phone2)) AS nd_phone2
        ,SUBSTRING(pc.mobile_number, 2, LENGTH(pc.mobile_number)) AS nd_company_mobile_number
        ,SUBSTRING(pcon.other_number, 2, LENGTH(pcon.other_number)) AS nd_office_number
        ,SUBSTRING(pcon.other_number_2, 2, LENGTH(pcon.other_number_2)) AS nd_office_number_2
        ,SUBSTRING(pcon.mobile_number, 2, LENGTH(pcon.mobile_number)) AS nd_mobile_number
        ,uv.image
        ,p.back_end_mid,p.front_end_mid,p.reporting_mid,p.pricing_type
        FROM partners p
        LEFT JOIN partner_companies pc ON p.id=pc.partner_id
        LEFT JOIN partner_mailing_addresses pma ON p.id=pma.partner_id
        LEFT JOIN partner_types pt on p.partner_type_id = pt.id
        LEFT JOIN partner_contacts pcon on p.id=pcon.partner_id and pcon.is_original_contact=1
        LEFT JOIN partner_payment_methods ppm on p.id=ppm.partner_id
        LEFT JOIN partner_payment_infos ppi on p.id=ppi.partner_id and ppi.is_default_payment = 1
        LEFT JOIN partner_dba_addresses pda on p.id=pda.partner_id
        LEFT JOIN partner_billing_addresses pba on p.id=pba.partner_id
        LEFT JOIN partner_shipping_addresses psa on p.id=psa.partner_id
        INNER JOIN users u on u.username = p.create_by
        LEFT JOIN users uv on uv.reference_id = p.id
        WHERE p.status IN ('A','I','T') AND p.id IN ($id)");

        if ($parent_id != -1) {
            $records .= DB::raw("  AND p.parent_id IN(" . $parent_id . ")");
        }

        if ($partner_type_id != -1) {
            $records .= DB::raw(" and pt.id in ($partner_type_id)");
        }
        $records .= DB::raw('limit 1');
        //dd($records);
        $results = DB::select($records);

        return $results;
    }

    public static function get_upline_partner($partner_id, $parent_id = -1, $partner_type_id = -1)
    {
        $records = DB::raw("select concat(pc.first_name,' ', pc.last_name) as upline_partner, pp.partner_id_reference,p.parent_id 
               ,ppc.company_name
               ,u.image as image 
               from partners p
               left join partner_contacts pc on pc.partner_id = p.parent_id and pc.is_original_contact = 1
               inner join partners pp on pp.id = p.parent_id
               inner join partner_companies ppc on ppc.partner_id = p.parent_id 
               left join users u on u.reference_id = p.id and u.is_original_partner = 1 
               where p.status IN ('A','I') and p.id = -1
               UNION DISTINCT
               select concat(pc.first_name,' ', pc.last_name) as upline_partner, p.partner_id_reference,p.id as parent_id
               ,ppc.company_name
               ,u.image as image 
               from partners p 
               left join partner_contacts pc on pc.partner_id = p.id and pc.is_original_contact = 1
               inner join partner_companies ppc on ppc.partner_id = p.id
               left join users u on u.reference_id = p.id and u.is_original_partner = 1 
               WHERE  p.status = 'A' and ");
        if ($partner_type_id != -1) {
            $records .= DB::raw(" p.partner_type_id IN (" . $partner_type_id . ")");
        } else {
            $records .= DB::raw(" p.partner_type_id IN (1,2,4,5,7)");
        }
        if ($parent_id != -1) {
            $records .= DB::raw(" and p.id IN (" . $parent_id . ")");
        }

        $results = DB::select($records);

        return $results;
    }

    public static function get_downline_partner($partner_id, $parent_id = -1, $partner_type_id = -1)
    {
        $records = DB::raw("select distinct concat(pc.first_name,' ', pc.last_name) as upline_partner, p.partner_id_reference,p.id as parent_id
               ,pcom.company_name as dba,
               u.image as image 
               from partners p
               left join partner_contacts pc on pc.partner_id = p.id and pc.is_original_contact = 1
               left join partner_companies pcom on p.id = pcom.partner_id
               left join users u on u.reference_id = p.id and u.is_original_partner = 1 
               WHERE p.status IN('A','I') and ");
        if ($partner_type_id != -1) {
            $records .= DB::raw(" p.partner_type_id IN (" . $partner_type_id . ")");
        } else {
            $records .= DB::raw(" p.partner_type_id IN (1,2,4,5,7)");
        }
        if ($parent_id != -1) {
            $records .= DB::raw(" and p.id IN (" . $parent_id . ")");
        }

        $results = DB::select($records);

        return $results;
    }

    public static function get_downline_partner_ids($partner_id)
    {
        $partner_ids = $partner_id;
        $id = $partner_id;
        requery:
        $records = DB::raw("select id
               from partners
               WHERE status = 'A' and parent_id in({$id})");

        $results = DB::select($records);
        if (!isset($results)) {
            goto exitquery;
        }
        $id = "-100";
        foreach ($results as $result) {
            $partner_ids .= ',' . $result->id;
            $id .= ',' . $result->id;
        }
        if ($id == "-100") {
            goto exitquery;
        }
        goto requery;
        exitquery:
        return ($partner_ids == "") ? -1 : $partner_ids;
    }

    public static function get_interested_products($partner_id)
    {
        $records = DB::select("select interested_products from partners where id=" . $partner_id);
        $results = '';
        if ($records[0]->interested_products) {
            if ($records[0]->interested_products !== "") {
                $results = DB::select("SELECT * FROM products WHERE id in (" . $records[0]->interested_products . ")");
            }
        }
        return $results;
    }

    public static function get_partners($id = -1, $partner_type_id = -1, $partner_id = -1, $product_access = -1, $status = "", $sort_by_field = "", $filter = "",$statusFilter = "'A','I','T','V'",$get_upline = true)
    {
        $cmd = 
            "SELECT DISTINCT 
                pt.description AS partner_type,
                IFNULL(pc.dba, '') dba,
                p.id AS partner_id,
                p.created_at,
                IFNULL(pc.company_name, pco.company_name) AS company_name,
                IFNULL(pc.email, pco.email) AS email,
                IFNULL(pc.phone1, pco.mobile_number) phone1, 
                IFNULL(pc.phone2, pco.other_number) phone2, 
                IFNULL(pc.state,pco.state) state,
                IFNULL(pc.country,pco.country) AS country_name,
                pc.country_code,
                CASE 
                    WHEN p.partner_type_id IN (1,2) THEN 'partner' 
                    WHEN p.partner_type_id IN (4,5) THEN 'partner'
                    WHEN p.partner_type_id IN (3) THEN 'merchant' 
                    WHEN p.partner_type_id IN (6) THEN 'lead' 
                    ELSE 'partner'
                END AS 'controller',
                pco.first_name,
                pco.last_name,
                IFNULL(is_verified_email,0) is_verified_email,
                IFNULL(is_verified_mobile,0) is_verified_mobile,
                IFNULL(p.merchant_mid,'') merchant_mid,
                IFNULL(p.merchant_url,'') merchant_url,
                IFNULL(p.credit_card_reference_id,'') credit_card_reference_id,
                IFNULL(p.federal_tax_id,'') federal_tax_id,
                IFNULL(p.merchant_processor,'') merchant_processor,
                IFNULL(p.services_sold,'') services_sold,
                IFNULL(p.bank_name,'') bank_name,
                IFNULL(p.bank_account_no,'') bank_account_no,
                IFNULL(p.bank_routing_no,'') bank_routing_no,
                IFNULL(p.withdraw_bank_name,'') withdraw_bank_name,
                IFNULL(p.withdraw_bank_account_no,'') withdraw_bank_account_no,
                IFNULL(p.withdraw_bank_routing_no,'') withdraw_bank_routing_no,
                IFNULL(p.authorized_rep,'') authorized_rep, 
                IFNULL(p.IATA_no,'') IATA_no, 
                IFNULL(p.tax_filing_name,'') tax_filing_name, 
                IFNULL(p.partner_id_reference,'') partner_id_reference, 
                p.billing_status,
                p.status,
                p.merchant_status_id,
                p.parent_id
            FROM partners p
            INNER JOIN partner_types pt ON pt.id = p.partner_type_id
            LEFT JOIN partner_companies pc ON p.id=pc.partner_id
            LEFT JOIN partner_contacts pco ON 
                pco.partner_id = p.id AND 
                pco.is_original_contact=1
            LEFT JOIN users u ON
                u.reference_id = p.id AND 
                u.is_partner=1 AND 
                u.is_original_partner=1";
                
        if ($product_access != -1) {
            $cmd .= " INNER JOIN product_orders po on po.partner_id = p.id and po.product_id in ({$product_access})";
        }
        $cmd .= " WHERE p.status IN ({$statusFilter}) ";
        if ($id != -1) {
            $cmd .= " AND p.parent_id IN({$id})";
        }
        if ($partner_type_id != -1) {
            $cmd .= " AND p.partner_type_id IN({$partner_type_id})";
        }
        $cmd .= $filter;
        if ($partner_id > 0) {
            $cmd .= "UNION DISTINCT SELECT pt.description as partner_type,ifnull(pc.dba,'')dba,p.id as partner_id
                ,p.created_at
                ,ifnull(pc.company_name,pco.company_name) as company_name,ifnull(pc.email,pco.email) as email
                ,ifnull(pc.phone1,pco.mobile_number) phone1, ifnull(pc.phone2,pco.other_number) phone2, ifnull(pc.state,pco.state) state
                ,ifnull(pc.country,pco.country) as country_name
                ,pc.country_code
                ,case when p.partner_type_id IN (1,2)  then 'partner' when p.partner_type_id IN (4,5) then 'partner' when p.partner_type_id IN (3) then 'merchant' when p.partner_type_id IN (6) then 'lead' else 'partner' end as 'controller'
                ,pco.first_name,pco.last_name
                ,ifnull(is_verified_email,0) is_verified_email ,ifnull(is_verified_mobile,0) is_verified_mobile,ifnull(p.merchant_mid,'') merchant_mid,ifnull(p.merchant_url,'') merchant_url,ifnull(p.credit_card_reference_id,'') credit_card_reference_id
                ,ifnull(p.federal_tax_id,'') federal_tax_id,ifnull(p.merchant_processor,'') merchant_processor,ifnull(p.services_sold,'') services_sold
                ,ifnull(p.bank_name,'') bank_name,ifnull(p.bank_account_no,'') bank_account_no,ifnull(p.bank_routing_no,'') bank_routing_no
                ,ifnull(p.withdraw_bank_name,'') withdraw_bank_name,ifnull(p.withdraw_bank_account_no,'') withdraw_bank_account_no,ifnull(p.withdraw_bank_routing_no,'') withdraw_bank_routing_no
                ,ifnull(p.authorized_rep,'') authorized_rep, ifnull(p.IATA_no,'') IATA_no, ifnull(p.tax_filing_name,'') tax_filing_name
                ,IFNULL(p.partner_id_reference,'') partner_id_reference
                , p.billing_status,p.status,
                p.merchant_status_id,p.parent_id
                FROM partners p
                INNER JOIN partner_types pt on pt.id = p.partner_type_id
                LEFT JOIN partner_companies pc ON p.id=pc.partner_id
                LEFT JOIN partner_contacts pco ON pco.partner_id = p.id and pco.is_original_contact=1
                LEFT JOIN users u on u.reference_id = p.id and u.is_partner = 1  and u.is_original_partner=1
                WHERE p.status IN({$statusFilter}) AND p.id= {$partner_id}";
            $cmd .= " AND p.partner_type_id IN({$partner_type_id})";
        }

        if ($status == 'today') {
            $cmd .= " AND DATE(p.created_at) = DATE(NOW())";
        }
        if ($status == 'month') {
            $cmd .= " AND MONTH(p.created_at) = MONTH(NOW()) AND YEAR(p.created_at) = YEAR(NOW())";
        }
        if ($status == 'year') {
            $cmd .= " AND YEAR(p.created_at) = YEAR(NOW())";
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
                $upline_partner_access = Partner::get_upline_partners_access($result->partner_id);
                if ($upline_partner_access != "") {
                    $result->upline_partners = Partner::get_upline_partner_info($upline_partner_access, true);
                } else {
                    $result->upline_partners = array();
                }

                //$r['upline_partners'] = array();
                $results[] = $result;
            }
            return $results;            
        }else{
            return $rs; 
        }

    }

    public static function get_upline_company($partner_id)
    {
        $id = array();
        $counter = 0;
        $original_partner_id = $partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        $cmd = "select id,parent_id from partners where id IN ({$partner_id})";
        $records = DB::raw($cmd);
        $records = DB::select($records);
        foreach ($records as $r) {
            if ($r->parent_id == $original_partner_id) {
                goto exitquery;
            }
            $id[$counter] = $r->parent_id;
            $temp_id = $temp_id . $r->parent_id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        if ($return_data == "") {
            return -1;
        }
        $cmd = "select id,parent_id from partners where id IN ({$return_data}) and parent_id = -1";
        $records = DB::raw($cmd);
        $records = DB::select($records);
        if (count($records) > 0) {
            return $r->id;
        } else {
            return false;
        }

    }

    public static function get_partner_attachment($partner_id)
    {
        $records = DB::raw("SELECT DISTINCT d.id, d.name
               FROM documents d
               left join partner_attachments pa on pa.document_id = d.id and partner_id={$partner_id}
               UNION DISTINCT
               SELECT document_id as id,name
               from partner_attachments
               WHERE partner_id={$partner_id}  ORDER BY name");

        $records = DB::select($records);

        $results = array();
        foreach ($records as $record) {
            if ($record->name !== "") {
                $documents_query = DB::raw("SELECT document_id as id,document_image,name,id as attachment_id
                        ,ifnull(created_at,'') as create_date, ifnull(create_by,'') as create_by
                        from partner_attachments
                        WHERE partner_id={$partner_id} and name='{$record->name}'");
                $documents = DB::select($documents_query);
                $record->details = $documents;
            } else {
                $record->details = array();
            }

            $results[] = $record;
        }

        return $results;
    }

    /**
     * Scopes
     */

    /**
     * Returns the query with merchant_mid where clause
     *
     * @param $query
     * @param $mid
     * @return mixed
     */
    public function scopeOfMID($query, $mid)
    {
        if (isset($mid)) {
            return $query->where('partner_id_reference', 'LIKE', '%' . $mid . '%');
        } else {
            return $query;
        }
    }

    /**
     * Returns the query with partner type id where clause
     *
     * @param $query
     * @param $partnerTypeId
     * @return mixed
     */
    public function scopeOfPartnerTypeId($query, $partnerTypeId)
    {
        if (isset($partnerTypeId)) {
            return $query->where('partner_type_id', '=', $partnerTypeId);
        } else {
            return $query;
        }
    }

    /**
     * Returns the query with cid where clause
     *
     * @param $query
     * @param $cid
     * @return mixed
     */
    public function scopeOfCID($query, $cid)
    {
        if (isset($cid)) {
            return $query->where('credit_card_reference_id', '=', $cid);
        } else {
            return $query;
        }
    }

    /**
     * Returns the query with merchant url where clause
     *
     * @param $query
     * @param $merchantUrl
     * @return mixed
     */
    public function scopeOfMerchantUrl($query, $merchantUrl)
    {
        if (isset($merchantUrl)) {
            return $query->where('merchant_url', '=', $merchantUrl);
        } else {
            return $query;
        }
    }

    /**
     * Scope for partner company
     *
     * @param $query
     * @param null $name
     * @param null $phone
     * @param null $email
     * @param null $state
     */
    public function scopeOfPartnerCompany($query, $name = null, $phone = null, $email = null, $state = null, $dba = null)
    {
        /**
         * Company name
         */
        if (isset($name)) {
            $query->whereHas('partner_company', function ($q) use ($name) {
                $q->where('company_name', 'LIKE', '%' . $name . '%');
            });
        }

        /**
         * DBA
         */
        if (isset($dba)) {
            $query->whereHas('partner_company', function ($q) use ($dba) {
                $q->where('dba', 'LIKE', '%' . $dba . '%');
            });
        }

        /**
         * Country Code
         */
        if (isset($phone)) {
            $phone = str_replace("-", "", $phone);
            $query->whereHas('partner_company', function ($q) use ($phone) {
                $q->whereRaw("REPLACE(CONCAT(country_code,mobile_number), '-', '') LIKE '%" . $phone . "%'");
            });
        }

        /**
         * Email
         */
        if (isset($email)) {
            $query->whereHas('partner_company', function ($q) use ($email) {
                $q->where('email', '=', $email);
            });
        }

        /**
         * State
         */
        if (isset($state)) {
            $query->whereHas('partner_company', function ($q) use ($state) {
                $q->where('state', '=', $state);
            });
        }

    }

    /**
     * Scope for Partner Contact
     *
     * @param $query
     * @param $number
     */
    public function scopeOfPartnerContact($query, $number)
    {
        if (isset($number)) {
            $query->whereHas('partnerContact', function ($query) use ($number) {
                $query->mobile_number = $number;
            });
        }
    }

    /**
     * Returns the query with parent_id where clause
     *
     * @param $query
     * @param $parent_id
     * @return mixed
     */
    public function scopeOfPID($query, $parent_id)
    {
        if (isset($parent_id)) {
            return $query->where('parent_id', '=', $parent_id);
        } else {
            return $query;
        }
    }

    public static function get_merchants_by_product_access($partner_id)
    {
        $cmd = DB::raw("select distinct pc.partner_id,pc.company_name,pcon.country_code,pcon.first_name,pcon.last_name,pcon.mobile_number,pc.email from partner_companies pc
                inner join partner_contacts pcon on pcon.partner_id = pc.partner_id and is_original_contact=1
                inner join product_orders po on po.partner_id = pc.partner_id
                where po.product_id in (select id from products where company_id={$partner_id})");

        $records = DB::select($cmd);

        $results = array();
        foreach ($records as $record) {
            $cmd = DB::raw("select distinct p.name from product_orders po
                    inner join products p on p.id = po.product_id
                    where partner_id={$record->partner_id}
                    and product_id in (select id from products where company_id={$partner_id})");
            $products = DB::select($cmd);
            $record->products = $products;
            $results[] = $record;
        }

        return $results;
    }

    public static function get_added_contacts($scope, $userid)
    {
        $ids = array();
        $counter = 0;
        $return_data = "";
        $temp_id = "";
        if ($scope == 'contacts') {
            $cmd = DB::raw("SELECT CASE WHEN cfr.recipient = " . $userid . " THEN cfr.sender_id WHEN cfr.recipient != " . $userid . " THEN cfr.recipient ELSE 0 END AS id
                    FROM users u
                    JOIN chat_friend_requests cfr
                    ON u.id = cfr.recipient
                    WHERE
                        cfr.is_accepted_or_not = 1 AND
                        (cfr.sender_id = " . $userid . " OR cfr.recipient = " . $userid . ")");
        }
        if ($scope == 'users') {
            $cmd = DB::raw("SELECT id
                  FROM users
                  WHERE status = 'A' AND id != " . $userid . " AND user_type_id NOT IN (3,4,5,6,7,8,9,10,11,12,13)");
        }
        $records = DB::select($cmd);

        foreach ($records as $r) {
            $ids[$counter] = $r->id;
            $temp_id = $temp_id . $r->id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $return_data = substr($temp_id, 0, strlen($temp_id) - 1);
        }
        if (!$records) {
            $return_data = 0;
        }
        return $return_data;
    }

    public static function get_not_partners_access_id_chat($scope, $merge_partners, $added_contacts)
    {
        $partner_id = auth()->user()->id;
        $id = array();
        $counter = 0;
        $original_partner_id = $partner_id;
        //$id[0]=$partner_id;
        $return_data = "";
        requery:
        $temp_id = "";
        if ($scope == 'agents' || $scope == 'partners' || $scope == 'admin_partners') {
            $cmd = DB::raw("SELECT u.id
                    FROM users u
                    LEFT JOIN partners p
                    ON u.username = p.partner_id_reference
                    WHERE
                        (p.id IN (" . $merge_partners . ") OR
                        u.id IN (" . $added_contacts . ")) AND
                        p.id != " . $partner_id . " AND
                        (u.status = 'A' OR p.status = 'A')");
            // -- AND u.username NOT LIKE 'U%'
        }
        // print_r($cmd);die();
        $records = DB::select($cmd);
        foreach ($records as $r) {
            if ($r->id == $original_partner_id) {
                goto exitquery;
            }
            $id[$counter] = $r->id;
            $temp_id = $temp_id . $r->id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            // goto requery;
        }
        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }
            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }
        if ($return_data == '') {
            $return_data = 0;
        }
        return $return_data;
    }

    public static function get_top_upline_partner($partner_id)
    {
        $id = array();
        $counter = 0;
        $original_partner_id = $partner_id;
        $return_data = "";
        $company_id = -1;

        requery:
        $temp_id = "";
        $cmd = "select id,parent_id,partner_type_id from partners where id IN ({$partner_id})";
        $cmd = DB::raw($cmd);
        $records = DB::select($cmd);
        foreach ($records as $r) {
            if ($r->parent_id == $original_partner_id) {
                goto exitquery;
            }
            if ($r->partner_type_id == 7) {
                $company_id = $r->id;
                goto exitquery;
            }
            $id[$counter] = $r->parent_id;
            $temp_id = $temp_id . $r->parent_id . ",";
            $counter++;
        }
        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }
        exitquery:
        return $company_id;
    }

    public static function get_partner_access($partner_id)
    {
        $id = array();
        $counter = 1;
        $id[0] = $partner_id;
        $original_partner_id = $partner_id;
        $return_data = "";

        requery:
        $temp_id = "";
        $cmd = "SELECT id, parent_id
                    FROM partners
                    WHERE parent_id
                    IN ({$partner_id})";
        $partners = Partner::whereIn('parent_id', $partner_id)->get();
        foreach ($partners as $partner) {
            if ($partner->id == $original_partner_id) {
                goto exitquery;
            }

            $id[$counter] = $partner->id;
            $temp_id = $temp_id . $partner->id . ",";
            $counter++;
        }

        if (strlen($temp_id) > 0) {
            $partner_id = substr($temp_id, 0, strlen($temp_id) - 1);
            goto requery;
        }

        exitquery:
        if (count($id) > 0) {
            $counter = 0;
            foreach ($id as $data) {
                $return_data = $return_data . $data . ",";
                $counter++;
            }

            $return_data = substr($return_data, 0, strlen($return_data) - 1);
        }

        return $return_data;
    }

    public static function product_sales($partner_id)
    {
        $cmd = "SELECT main_product.id, main_product.name, sum(ivd.amount) as total
        FROM invoice_headers ih
        inner join partners merchant on merchant.id = ih.partner_id
        inner join partners upline on merchant.parent_id = upline.id
        inner join invoice_details ivd on ivd.invoice_id = ih.id
        inner join products p on ivd.product_id = p.id
        inner join products main_product on main_product.id = p.parent_id
        WHERE upline.company_id = ({$partner_id}) AND ih.status = 'P'
        group by main_product.id, main_product.name
        order by total desc";

        $result = DB::select(DB::raw($cmd));

        return $result;
    }

    public static function company_sales($partner_id)
    {
        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $agentSales = DB::select(DB::raw($cmd));
        $salesPerAgent = "";
        foreach ($agentSales as $agent) {

            $salesPerAgent .= '{
                                "type": "spline",
                                "showInLegend": true,
                                "yValueFormatString": "##.00m",
                                "name": "' . $agent->company_name . '",
                                "dataPoints": [';
            for ($i = 60; $i >= 0; $i--) {
                $x = $i + 1;
                $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                        inner join partners merchant on merchant.id = ih.partner_id
                        inner join partners upline on merchant.parent_id = upline.id
                        inner join partner_companies pc on upline.company_id = pc.partner_id
                        where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL ({$i}) DAY
                        and ih.invoice_date >= NOW() - INTERVAL ({$x}) DAY
                        and upline.company_id = ({$partner_id})
                        group by pc.company_name,pc.partner_id
                        order by total desc";
                $result = collect(DB::select(DB::raw($cmd)))->first();

                if (isset($result)) {
                    if ($i != 0) {
                        $salesPerAgent .= '{ "label": "' . $i . '", "y": ' . $result->total . ' },';
                    } else {
                        $salesPerAgent .= '{ "label": "' . $i . '", "y": ' . $result->total . ' }';
                    }
                } else {
                    if ($i != 0) {
                        $salesPerAgent .= '{ "label": "' . $i . '", "y": 0.00 },';
                    } else {
                        $salesPerAgent .= '{ "label": "' . $i . '", "y": 0.00 } ';
                    }
                }
            }

            $salesPerAgent .= ']}';
        }
        return response()->json($salesPerAgent);
    }

    public function company_sales_increase($partner_id)
    {
        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 60 DAY
                and ih.invoice_date >= NOW() - INTERVAL 120 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $originalSales = DB::select(DB::raw($cmd));
        if (empty($originalSales)) {
            $originalSales = 0.00;
        } else {
            $originalSales = $originalSales[0]->total;
        }

        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $currentSales = DB::select(DB::raw($cmd));
        if (empty($currentSales)) {
            $currentSales = 0.00;
        } else {
            $currentSales = $currentSales[0]->total;
        }
        return $currentSales;
    }

    public function percentage_sales($partner_id)
    {
        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 60 DAY
                and ih.invoice_date >= NOW() - INTERVAL 120 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $originalSales = DB::select(DB::raw($cmd));
        if (empty($originalSales)) {
            $originalSales = 0.00;
        } else {
            $originalSales = $originalSales[0]->total;
        }

        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $currentSales = DB::select(DB::raw($cmd));
        if (empty($currentSales)) {
            $currentSales = 0.00;
        } else {
            $currentSales = $currentSales[0]->total;
        }

        $percent = $currentSales - $originalSales;
        if($originalSales != 0)
        {
            $percent = $percent/$originalSales;
        }
        $percent = $percent * 100;

        return $percent;
    }

    public function percentage_sales_width($partner_id)
    {
        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 60 DAY
                and ih.invoice_date >= NOW() - INTERVAL 120 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $originalSales = DB::select(DB::raw($cmd));
        if (empty($originalSales)) {
            $originalSales = 0.00;
        } else {
            $originalSales = $originalSales[0]->total;
        }

        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners merchant on merchant.id = ih.partner_id
                inner join partners upline on merchant.parent_id = upline.id
                inner join partner_companies pc on upline.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                and upline.company_id = ({$partner_id})
                group by pc.company_name,pc.partner_id
                order by total desc";
        $currentSales = DB::select(DB::raw($cmd));
        if (empty($currentSales)) {
            $currentSales = 0.00;
        } else {
            $currentSales = $currentSales[0]->total;
        }

        $percent = $currentSales - $originalSales;
        if($originalSales != 0)
        {
            $percent = $percent/$originalSales;
        }
        $percent = $percent * 100;

        return $percent;
    }

    public function invoiceHeaders()
    {
        return $this->hasMany('App\Models\InvoiceHeader');
    }

    public function products()
    {
        return $this->hasManyThrough('App\Models\Product','App\Models\PartnerProduct', 'partner_id', 'id', 'id', 'product_id' );
    }
    
    public function partnerProduct()
    {
        return $this->hasMany('App\Models\PartnerProduct', 'partner_id', 'id');
    }

    public function invoiceDetails()
    {
        return $this->hasManyThrough('App\Models\InvoiceDetail', 'App\Models\InvoiceHeader', 'partner_id', 'id', 'invoice_id', 'id');
    }

    public function partner_parent()
    {
        return $this->hasOne('App\Models\Partner', 'id', 'parent_id');
    }

     public function partner_company_parent()
    {
        return $this->hasOne('App\Models\PartnerCompany', 'partner_id', 'parent_id');
    }

    public function merchantStatus()
    {
        return $this->belongsto('App\Models\MerchantStatus');
    }

    public function merchantBranches()
    {
        return $this->hasMany('App\Models\Partner', 'parent_id');
    }

    public function getUplinesAttribute() 
    {
        $partner = $this;   
        $uplines = collect([]);
        while ($partner->upline !== null) {
            $uplines->push($partner->upline);
            $partner = $partner->upline;
        }
        
        return $uplines;
    }

    public function upline()
    {
        return $this->belongsTo(Partner::class, 'parent_id');
    }

    public function directDownlines()
    {
        return $this->hasMany(Partner::class, 'parent_id')->with('directDownlines');
    }

    public function getDownlinesAttribute()
    {
        $partner = $this;
        
        if (empty($partner->directDownlines)) {
            return collect([]);
        } else {
            return $this->getDownlines($partner->directDownlines);
        }
    }

    public function getDownlines($downlines)
    {
        $partners = collect([]);

        foreach ($downlines as $partner) {
            $partners->push($partner);

            $partnerClone = clone $partner;
            if ($partner->directDownlines->count() != 0)
                $partners = $partners->merge($this->getDownlines($partner->directDownlines));
        }

        return $partners;
    }

    public static function getProductTemplateID($product_id)
    {
        $rs = DB::select(DB::raw("Select template_id from product_template_details pd
            inner join products p on p.id = pd.product_id
            where p.parent_id in({$product_id})
            group by template_id"));
        $ids = "-1";
        foreach ($rs as $r) {
            $ids = $ids . "," . $r->template_id;
        }
        return $ids;
    }

    public function productsR()
    {
        return $this->belongsToMany(
            'App\Models\Product',
            'partner_products', 
            'partner_id', 
            'product_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class, 
            'user_companies', 
            'company_id',
            'user_id');
    }
}