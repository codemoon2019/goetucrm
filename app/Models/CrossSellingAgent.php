<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CrossSellingAgent extends Model
{
	protected $table = 'cross_selling_agents';
	protected $guarded = [];

    public static function get_cross_selling_agents($company_id)
    {
        $rs = DB::select(DB::raw("select p.id as agent_id, com.id as company_id,pcom.company_name as company,pc.company_name as agent,ifnull(ca.status,'D') as status from partners p
				inner join partner_companies pc on p.id = pc.partner_id
				inner join partners com on com.id = p.company_id
				inner join partner_companies pcom on com.id = pcom.partner_id
				left join cross_selling_agents ca on ca.agent_id = p.id and ca.partner_id = {$company_id}
				where p.partner_type_id = 1 and p.company_id <> {$company_id} order by status,company,agent"));

        return $rs;
    }


}
