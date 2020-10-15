<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PartnerProduct extends Model
{
    protected $table = 'partner_products';

    public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }

    public function partner()
    {
        return $this->belongsTo('App\Models\Partner');
    }

    public function frequency()
    {
        return $this->hasOne('App\Models\PaymentFrequency','name','payment_frequency');
    }

    public static function get_partner_products($partner_id){
    	$results =DB::select(DB::raw("SELECT parent.id as parent_id, parent.name as parent_name, pc.name as category_name,pc.id as category_id,pp.product_id,p.name, pp.buy_rate, downline_buy_rate, other_buy_rate, payment_frequency, DATE_FORMAT(due_date,'%m/%d/%Y')as due_date, split_type,is_split_percentage, upline_percentage, downline_percentage, pricing_option,price 
			FROM partner_products pp INNER JOIN products p on pp.product_id = p.id 
			INNER JOIN products parent ON parent.id=p.parent_id 
			INNER JOIN product_categories pc on pc.id = p.product_category_id 
			WHERE pp.partner_id=".$partner_id." order by parent.name,pc.name")); 

    	return $results;
    }
}
