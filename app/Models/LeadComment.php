<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeadComment extends Model
{
    protected $table = 'lead_comments';

    public static function get_lead_comment($partner_id, $include_internal = 0)
    {
        $records=DB::raw("SELECT lc.partner_id,lc.id as comment_id, lc.comment,lc.created_at, u.last_name, u.first_name,lc.parent_id,lc.lead_status 
              FROM lead_comments lc
              LEFT JOIN users u ON lc.user_id=u.id
              WHERE partner_id={$partner_id}
              AND parent_id=-1");
            
        if($include_internal == 0)
        {
            $records .= DB::raw(" AND is_internal = ".$include_internal."
            ORDER BY lc.updated_at DESC, parent_id");
        }else{
            $records .=DB::raw(" ORDER BY lc.updated_at DESC, parent_id");
        }

        $rs = DB::select($records);
        
        $results = array();
        foreach($rs as $r)
        {
            $parent_id = $r->comment_id;
            $r->sub_comments = DB::select(DB::raw("SELECT lc.comment,lc.created_at, u.last_name, u.first_name,lc.parent_id,lc.lead_status 
              FROM lead_comments lc 
              LEFT JOIN users u ON lc.user_id=u.id
              WHERE partner_id = ".$partner_id."
              AND parent_id=".$parent_id."
              ORDER BY created_at")); //ad_sub_comment($parent_id, $partner_id);
            
            $results[] = $r;
        }
        
        return $results;
    }
}
