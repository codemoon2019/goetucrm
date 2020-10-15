<?php

namespace App\Models;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    /**
     * @var string const
     */
    const PRODUCT_TYPE_SERVICE = "SERVICE";
    const PRODUCT_TYPE_NON_INVENTORY = "NON-INVENTORY";
    const PRODUCT_TYPE_INVENTORY = "INVENTORY";
    const PRODUCT_STATUS_ACTIVE = "A";

    const STATUS_ACTIVE = 'A';
    const STATUS_DELETED = 'D';

    /**
     * Table name
     * @var string
     */
    protected $table = 'products';


    /**
     * Product has many departments N:M
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userTypes()
    {
        return $this->belongsToMany("App\\Models\\UserType","user_type_product_accesses","product_id","user_type_id");
    }

    public function categories()
    {
        return $this->hasMany('App\Models\ProductCategory','product_id','id');
    }

    public function modules()
    {
        return $this->hasMany('App\Models\ProductModule','product_id','id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductCategory','id','product_category_id');
    }

    public function payment_type()
    {
       return $this->hasOne('App\Models\ProductPaymentType','id','product_payment_type');
    }

    public function subproducts()
    {
       return $this->hasMany('App\Models\Product','parent_id','id');
    }

    public function mainproduct()
    {
       return $this->hasOne('App\Models\Product','id','parent_id');
    }

    public function inventoryDetails()
    {
        return $this->hasMany('App\Models\InventoryDetail', 'product_id', 'id');
    }

    public function invoiceFrequencies()
    {
        return $this->hasMany('App\Models\InventoryFrequency', 'product_id', 'id');
    }

    public function partnerCompany()
    {
        return $this->belongsTo('App\Models\PartnerCompany', 'company_id', 'partner_id');
    }

    public function productType()
    {
        return $this->belongsTo('App\Models\ProductType', 'product_type_id', 'id');
    }



    public static function get_parent_product_id($sub_product_id)
    {
        $cmd = "select distinct parent_id from products where id IN ({$sub_product_id})";
        $records = DB::raw($cmd);
        $result = DB::select($records);
        return $result;
    } 

    public static function get_child_products($id,$parent_id)
    {
        $cmd = "SELECT p.*,pp.payment_frequency,case pp.split_type  when 'First Buy Rate' then round(pp.downline_buy_rate,2)
                else round(pp.other_buy_rate,2) end as amount,p.product_category_id,pp.srp,pp.mrp FROM products p
                INNER JOIN partner_products pp ON pp.partner_id={$parent_id} AND p.id=pp.product_id
                WHERE p.parent_id = {$id} order by p.name";
        $records = DB::raw($cmd);
        $result = DB::select($records);
        return $result;
    }

    public static function api_get_products($product_id="", $partner_id="-1", $product_type_id=-1,$except=-1){
        //DB::enableQueryLog();
        $records = DB::raw("SELECT p.id,p.name,p.description,p.single_selection,CASE WHEN pp.buy_rate IS NULL THEN  p.buy_rate  ELSE pp.downline_buy_rate END as buy_rate,pp.payment_frequency  
            FROM products p 
            LEFT JOIN ");
        if ($product_type_id==2) {
            $records .= DB::raw("partner_product_ccs pp ");    
        } else {
            $records .= DB::raw("partner_products pp ");
        }
       
        $records.= DB::raw(" ON pp.partner_id={$partner_id} AND p.id=pp.product_id WHERE p.parent_id=-1 and p.status = 'A'");

        if ($product_id != ""){
            $records .= DB::raw(" AND p.id IN (".$product_id.")");
        }
        if ($product_type_id != -1){
            $records .= DB::raw(" AND p.product_type_id IN (".$product_type_id.")");
        }
        if ($except != -1) {
            $records .= DB::raw(" AND p.id NOT IN(".$except.")");
        }
        $records .= DB::raw(" order by name");
        
        $result = DB::select($records);
        //dd(DB::getQueryLog());
        return $result;
    }

    public static function api_get_company_products($company_id){

      return DB::select(DB::raw("select p2.id,p2.name,p2.description,p2.company_id from partner_products pp
                inner join products p on p.id = pp.product_id 
                inner join products p2 on p.parent_id = p2.id 
                where pp.partner_id = {$company_id} and p2.status = 'A'
                group by p2.id,p2.name,p2.description"));
    }

    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == -1 || $companyId == null) {
            return $query;
        }

        return $query->where('company_id', $companyId);
    }


    public static function get_partner_products($partner_id){
      return DB::select(DB::raw("select p2.id as main_id,p.id as sub_id,cat.id as cat_id,p2.name as main_product,cat.name as category,p.name as sub_product,c.type from partner_products pp
            inner join products p on p.id = pp.product_id
            inner join product_categories cat on cat.id = p.product_category_id
            inner join products p2 on p2.id = p.parent_id
            left join commissions c on c.partner_id = pp.partner_id and c.product_id = pp.product_id
            where pp.partner_id = {$partner_id}
            order by p2.name,cat.name"));
    }

    public function partnerProduct()
    {
        return $this->belongsTo('App\Models\PartnerProduct', 'product_id', 'id');
    }

    public function scopePartnerProductInvoice($partnerId)
    {
        return Partner::find($partnerId)->with('invoiceHeaders');
    }
    
    public function invoiceHeaders()
    {
        return $this->hasMany("App\Models\InvoiceHeader");
    }

    public function invoiceDetails()
    {
        return $this->hasMany("App\Models\InvoiceDetail");
    }

    public function productOrders()
    {
        return $this->hasMany("App\Models\ProductOrder");
    } 

    public function scopeIsActive($query)
	{
		return $query->where('status', '<>', 'D');
    }
    

    public function getDisplayPictureUrlAttribute()
    {
        return Storage::url($this->display_picture);
    }



    public function taskTemplate()
    {
        return $this
            ->hasOne(SubTaskTemplateHeader::class, 'product_id', 'id')
            ->with('subTaskTemplates');
    }

    public function ticketIssueTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function ticketReasons()
    {
        return $this->hasMany(TicketReason::class);
    }

    public function partners()
    {
        return $this->belongsToMany(
            'App\Models\Partner',
            'partner_products', 
            'product_id', 
            'partner_id');
    }

    public function scopeWithTicketAssignees($query) {
        $query->with(['userTypes' => function($query) {
            $columns = [
                'user_types.id',
                'user_types.description',
                'user_types.company_id',
                'user_types.head_id'
            ];

            $query
                ->select($columns)
                ->with('partnerCompany:id,partner_id,company_name')
                ->with(['users' => function($query) {
                    $columns = [
                        'users.id',
                        'users.image',
                        'users.first_name',
                        'users.last_name',
                        'users.user_type_id'
                    ];

                    $query
                        ->select($columns)
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }])
                ->isActive()
                ->whereHas('partnerCompany', function($query) {
                    $query->isActive();
                })
                ->whereHas('users', function ($query) {
                    $query->isActive();
                });
        }]);
    }
}
