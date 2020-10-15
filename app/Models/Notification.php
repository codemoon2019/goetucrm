<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $guarded = [];

    public static function get_new_messages_count()
    {
        $cmd = DB::raw("SELECT count(id) message_count from notifications where recipient='".auth()->user()->username."' and status='N'");

        $row = DB::select($cmd);

        if (!(isset($row[0]->message_count))) {
            return 0;
        } else { 
            return $row[0]->message_count;
        }
    }

    public static function get_notifications()
    {     
        $username = auth()->user()->username;
        $query = 
            "SELECT
                IFNULL(pc.company_name, '') as merchant,
                source_id,
                subject,
                message,
                is_starred,
                redirect_url,
                recipient,
                n.id,
                n.partner_id,
                n.status,
                n.created_at,
                n.create_by,
                n.updated_at,
                n.update_by,
                DATE_FORMAT(n.created_at, '%m/%d/%Y') as create_date,
                concat(u.first_name, ' ', u.last_name) as sent_by,
                p.partner_id_reference
            FROM notifications n
            LEFT JOIN users u ON u.username = n.create_by
            LEFT JOIN partner_companies pc ON pc.partner_id = n.partner_id
            LEFT JOIN partners p ON p.id = n.partner_id
            WHERE recipient = '{$username}'
            ORDER BY n.created_at DESC";

        return DB::select(DB::raw($query));
    }

    public static function get_available_task_assigments()
    {
        $cmd = DB::raw("select o.partner_id,h.order_id,h.name as task_name
                ,d.name as sub_task,DATE_FORMAT(d.due_date,'%m/%d/%Y') due_date,d.task_no,pc.company_name
                ,concat('merchant/product_comment/',o.partner_id,'&order_id=',h.order_id) as redirect_url
                from sub_task_headers h 
                inner join sub_task_details d on d.sub_task_id = h.id
                inner join product_orders o on o.id = h.order_id
                inner join partner_companies pc on pc.partner_id = o.partner_id
                where find_in_set('".auth()->user()->id."',assignee) <> 0 and ifnull(d.status,'') = ''
                order by d.updated_at desc
                ");

        $result = DB::select($cmd);
        
        return $result;
    }
}
