<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class PartnerPaymentInfo extends Model
{
    protected $table = 'partner_payment_infos';

    public function partner()
    {
        return $this->belongTo('App\Models\Partner', 'partner_id', 'id');
    }

    public static function get_payment_details($partner_id, $payment_type_id)
    {
        $cmd ="select id, partner_id, payment_type_id, case when is_default_payment =1 then 'YES' else 'NO' end as is_default_payment, bank_name, 
                routing_number, bank_account_number, cardholder_name, lpad(right(credit_card_no,4),length(credit_card_no),'x') credit_card_no, 
                expiration_date, address1, address2, city, state, 
                zip, create_by, created_at, update_by, updated_at, 
                status
                from partner_payment_infos 
               WHERE status='A' 
               and partner_id={$partner_id} and payment_type_id={$payment_type_id}";
        $records = DB::raw($cmd);
        $results = DB::select($records);
        return $results;
    }
}
