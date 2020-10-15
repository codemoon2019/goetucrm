<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PartnerType extends Model
{

    const PARTNER_TYPE_STATUS_ACTIVE = "A";
    const PARTNER_TYPE_STATUS_INACTIVE = "I";
    const PARTNER_TYPE_STATUS_DELETED = "D";

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'partner_types';

    /**
     * Get partner Types
     *
     * @param string $access
     * @param bool $selected_partners
     * @param bool $selected_agents
     * @param bool $selected_leads
     * @return array
     */
    public static function get_partner_types($access="", $selected_partners=false, $selected_agents=false, $selected_leads=false){
    	$records =DB::raw("SELECT * FROM partner_types WHERE status='A' ");
        if ($access !="") {
            $records .= DB::raw(" AND id IN (".$access.")");
        }
        if ($selected_partners){
            $records .= DB::raw(" AND included_in_partners=1");
        }
        if ($selected_agents){
            $records .= DB::raw(" AND included_in_agents=1");
        }
        if ($selected_leads){
            $records .= DB::raw(" AND included_in_leads=1");
        }
        $records .= DB::raw(" order by sequence");
		
		$results = DB::select($records);

		return $results;
    }

    public function draftPartner()
    {
        return $this->belongsTo("App\Models\Drafts\DraftPartner");
    }

}
