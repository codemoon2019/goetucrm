<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IncomingLead extends Model
{
    protected $table = 'incoming_leads';

    public static function get_incoming_leads($id,$partner_type){
    	$records = DB::raw("select l.id, l.assigned_id, l.partner_id, l.partner_type_id, 
              l.creator_id, l.created_at, l.create_by, l.updated_at, 
              l.update_by, case l.status when 'N' then 'New' when 'A' then 'Accepted' when 'E' then 'Expired' when 'D' then 'Declined' end as request_status
              ,case when l.creator_id = -1 then 'Admin' when l.creator_id != -1 then concat(pc_creator.first_name,' ',pc_creator.last_name) end as assigned_by
              ,concat(pc_assignee.first_name,' ',pc_assignee.last_name) as assignee
              ,pt.name as partner_type
              from 
              incoming_leads l 
              left join partner_contacts pc_creator on l.creator_id=pc_creator.partner_id and pc_creator.is_original_contact=1
              left join partner_contacts pc_assignee on pc_assignee.partner_id=l.partner_id and pc_assignee.is_original_contact=1
              left join partner_types pt on l.partner_type_id=pt.id
              where l.assigned_id=".$id." AND l.partner_type_id = ".$partner_type);
              // ,concat(pc_creator.first_name,' ',pc_creator.last_name) as assigned_by

    	$results = DB::select($records);

    	return $results;
    }
    public static function get_incoming_lead_info_by_id($id){
    	$records = DB::raw("select l.id, l.assigned_id, l.partner_id, l.partner_type_id, 
              l.creator_id, l.created_at, l.create_by, l.updated_at, 
              l.update_by, case l.status when 'N' then 'New' when 'A' then 'Accepted' when 'E' then 'Expired' when 'D' then 'Declined' end as request_status
              ,concat(pc_creator.first_name,' ',pc_creator.last_name) as assigned_by
              ,pc_creator.email as assigned_by_email
              ,concat(pc_assignee.first_name,' ',pc_assignee.last_name) as assignee
              ,pt.name as partner_type
              ,p_assignee.partner_id_reference
              from 
              incoming_leads l 
              left join partner_contacts pc_creator on l.creator_id=pc_creator.partner_id and pc_creator.is_original_contact=1
              left join partner_contacts pc_assignee on pc_assignee.partner_id=l.partner_id and pc_assignee.is_original_contact=1
              left join partners p_assignee on p_assignee.id=l.partner_id 
              left join partner_types pt on l.partner_type_id=pt.id
              where l.id=".$id);

    	$results = DB::select($records);

    	return $results;
    }
}
