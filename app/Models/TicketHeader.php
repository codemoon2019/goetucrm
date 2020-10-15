<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Access;
use App\Scopes\NonDeletedTicketScope;
use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;

class TicketHeader extends Model
{
    use ActiveTrait, ActorTrait;

    /**
     * Due by
     */
    const TICKET_DUE_BY_OVERDUE = "overdue";
    const TICKET_DUE_BY_TODAY = "today";
    const TICKET_DUE_BY_TOMORROW = "tomorrow";
    const TICKET_DUE_BY_NEXT_EIGHT_HOURS = "next 8 hours";

    /**
     * Status
     */
    const TICKET_STATUS_MERGE = "M";
    const TICKET_STATUS_CLOSE = "C";
    const TICKET_STATUS_CHANGED_STATUS = "TICKET_STATUS_CHANGED_STATUS";

    const TICKET_STATUS_NEW = "N";
    const TICKET_STATUS_OPEN = "O";
    const TICKET_STATUS_PENDING = "p";
    const TICKET_STATUS_SOLVED = "S";
    const TICKET_STATUS_DELETED = "D";
    const TICKET_STATUS_MERGED = "M";

    /**
     * Type
     */
    const TICKET_TYPE_SUCCESS = "success";
    const TICKET_TYPE_FAILED = "failed";

    const TICKET_PARENT_ID_NEGATIVE = -1;

    /**
     * Filter Type
     */
    const FILTER_TICKET_UNASSIGNED_TICKETS = "FILTER_TICKET_UNASSIGNED_TICKETS";
    const FILTER_TICKET_MY_CREATED_TICKETS = "FILTER_TICKET_MY_CREATED_TICKETS";
    const FILTER_TICKET_MY_UNSOLVED_TICKETS = "FILTER_TICKET_MY_UNSOLVED_TICKETS";
    const FILTER_TICKET_MY_PENDING_TICKETS = "FILTER_TICKET_MY_PENDING_TICKETS";
    const FILTER_TICKET_MY_NEW_TICKETS = "FILTER_TICKET_MY_NEW_TICKETS";
    const FILTER_TICKET_MY_CANCELLED_TICKETS = "FILTER_TICKET_MY_CANCELLED_TICKETS";
    const FILTER_TICKET_MY_RESOLVED_TICKETS = "FILTER_TICKET_MY_RESOLVED_TICKETS";
    const FILTER_TICKET_GROUP_UNSOLVED_TICKETS = "FILTER_TICKET_GROUP_UNSOLVED_TICKETS";
    const FILTER_TICKET_GROUP_PENDING_TICKETS = "FILTER_TICKET_GROUP_PENDING_TICKETS";
    const FILTER_TICKET_GROUP_NEW_TICKETS = "FILTER_TICKET_GROUP_NEW_TICKETS";
    const FILTER_TICKET_GROUP_CANCELLED_TICKETS = "FILTER_TICKET_GROUP_CANCELLED_TICKETS";
    const FILTER_TICKET_GROUP_RESOLVED_TICKETS = "FILTER_TICKET_GROUP_RESOLVED_TICKETS";
    const FILTER_TICKET_RECENTLY_UPDATED = "FILTER_TICKET_RECENTLY_UPDATED";
    const FILTER_TICKET_MY_CLOSED_TICKETS = "FILTER_TICKET_MY_CLOSED_TICKETS";
    const FILTER_TICKET_GROUP_CLOSED_TICKETS = "FILTER_TICKET_GROUP_CLOSED_TICKETS";

    const TICKET_FILTERS = [
        'All Tickets',
        'All Unassigned Tickets',
        'All Unsolved Tickets',
        'All Solved Tickets',
        'All Deleted Tickets',

        'My Tickets',
        'My Unassigned Tickets',
        'My Unsolved Tickets',
        'My Solved Tickets',
        'My Deleted Tickets',
    ];

    protected $table = 'ticket_headers';
    protected $dates = [
        'created_at', 
        'updated_at', 
        'due_date', 
        'responsed_at_department', 
        'responsed_at_assignee',
        'first_replied_at',
        'finished_at'
    ];
    
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new NonDeletedTicketScope);
    }

    public function getInvolvedUsersAttribute()
    {
        $users = $this->ccs;
        $users->push($this->createdBy);
        $users->push($this->departmentHead);
        $users->push($this->assignedTo);
        
        return $users->filter();
    }

    /**
     * Ticket has one partner company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partnerCompany()
    {
        return $this->hasOne("App\\Models\\PartnerCompany", "id", "partner_id");
    }

    /**
     * Ticket has one partner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne("App\\Models\\Partner", "id", "partner_id");
    }

    /**
     * Ticket details
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketDetails()
    {
        return $this->hasMany("App\\Models\\TicketDetail", "ticket_id", "id");
    }

    public function ticketType()
    {
        return $this->hasOne(TicketType::class, 'id', 'type');
    }

    public function ticketIssueType()
    {
        return $this->hasOne(TicketType::class, 'id', 'type');
    }

    public function ticketReason()
    {
        return $this->hasOne(TicketReason::class, 'id', 'reason');
    }

    /**
     * Ticket header has one user type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userType()
    {
        return $this->hasOne("App\\Models\\UserType", "id", "department");
    }

    /**
     * Ticket header has many CC (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ccs()
    {
        return $this->belongsToMany("App\Models\User", 'ticket_cc', 
            'ticket_header_id', "user_id")->withTimestamps();
    }

    /**
     * Ticket header has many attachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany("App\Models\TicketAttachment", 'ticket_header_id', 'id');
    }

    /**
     * Ticket header has many Assignees (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo("App\Models\User", "assignee", "id");
    }

    public function assignedTo()
    {
        return $this->belongsTo("App\Models\User", "assignee", "id");
    }

    public function subTaskDetail()
    {
        return $this->belongsTo('App\Models\SubTaskDetail');
    }

    public function subtask()
    {
        return $this->belongsTo(SubTaskDetail::class, 'sub_task_detail_id');
    }

    /**
     * Ticket header has many Assignees (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requester()
    {
        return $this->belongsTo("App\Models\User", "requester_id", "id");
    }

    /**
     * Ticket header has one partner type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partnerType()
    {
        return $this->hasOne("App\\Models\\PartnerType", "id", "partner_id");
    }

    /**
     * Ticket header has one product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne("App\\Models\\Product", "id", "product_id");
    }

    /**
     * Ticket header has one ticket status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ticketStatus()
    {
        return $this->hasOne("App\\Models\\TicketStatus", "code", "status");
    }

    public function ticketPriority()
    {
        return $this->hasOne(TicketPriority::class, 'code', 'priority');
    }

    /**
     * Ticket header has one creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userTypes()
    {
        return $this->hasOne("App\\Models\\UserType", "id", "department");
    }

    /**
     * Scope for Ticket type filter
     *
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeOfType($query, $type)
    {
        if (isset($type)) {
            return $query->where('type', '=', $type);
        } else {
            return $query;
        }
    }

    /**
     * Scope for Product filter
     *
     * @param $query
     * @param $productId
     * @return mixed
     */
    public function scopeOfProduct($query, $productId)
    {
        if (isset($productId)) {
            return $query->where('product_id', '=', $productId);
        } else {
            return $query;
        }
    }

    /**
     * Scope for department filter
     *
     * @param $query
     * @param $departmentId
     * @return mixed
     */
    public function scopeOfDepartment($query, $departmentId)
    {
        if (isset($departmentId)) {
            return $query->where('department', '=', $departmentId);
        } else {
            return $query;
        }

    }

    /**
     * Scope for department filter
     *
     * @param $query
     * @param $departmentId
     * @return mixed
     */
    public function scopeWhereRequester($query, $requesterId)
    {
        if (isset($requesterId)) {
            return $query->where('requester_id', $requesterId);
        } else {
            return $query;
        }
    }

    /**
     * Scope for assignees: Check if assigneeId will be found on the set
     *
     * @param $query
     * @param $assignees
     * @return mixed
     */
    public function scopeOfAssignedTo($query, $assignees)
    {
        if (isset($assignees)) {
            return $query->whereRaw('FIND_IN_SET(' . $assignees . ', assignee) > 0');
        } else {
            return $query;
        }
    }

    /**
     * Scope for partner
     *
     * @param $query
     * @param $partnerId
     * @return mixed
     */
    public function scopeOfPartner($query, $partnerId)
    {
        if (isset($partnerId)) {
            return $query->where('partner_id', '=', $partnerId);
        } else {
            return $query;
        }
    }

    public function scopeWherePriority($query, $priorityCode = null)
    {
        if (is_null($priorityCode)) {
            return $query;
        }

        return $query->where('priority', $priorityCode);
    }

    public function scopeWhereStatus($query, $statusCode)
    {
        if (is_null($statusCode)) {
            return $query;
        }

        return $query->where('status', $statusCode);
    }

    public function scopeWhereStatusIn($query, $statusCodes)
    {
        if (is_null($statusCodes)) {
            return $query;
        }

        return $query->whereIn('status', $statusCodes);
    }

    public function scopeWhereDepartmentIn($query, $departmentIds)
    {
        if (is_null($departmentIds))
            return $query;

        if ($departmentIds == [])
            return $query->where('department', '<>', -1);

        return $query->whereIn('department', $departmentIds);
    }

    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == -1)
            return $query;

        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for created time
     *
     * @param $query
     * @param $createdData
     * @return mixed
     */
    public function scopeOfCreated($query, $createdData)
    {
        if (isset($createdData)) {
            return $query->whereRaw($createdData);
        } else {
            return $query;
        }
    }

    /**
     * Scope for due by
     *
     * @param $query
     * @param $dueBy
     * @return mixed
     */
    public function scopeOfDueBy($query, $dueBy)
    {
        if (isset($dueBy)) {
            return $query->whereRaw($dueBy);
        } else {
            return $query;
        }
    }

    public function scopeIsNew($query)
    {
        return $query->where('status', 'N');
    }

    public function scopeIsOpen($query)
    {
        return $query->where('status', 'O');
    }

    public function scopeIsPending($query)
    {
        return $query->where('status', 'P');
    }

    public function scopeIsSolved($query)
    {
        return $query->where('status', 'S');
    }

    public function scopeIsUnsolved($query)
    {
        return $query->where('status', 'O')
            ->orWhere('status', 'P')
            ->orWhere('status', 'N');
    }

    public static function get_ticket_list($params)
    {
        $cmd = "select h.id,subject,ifnull(concat(u.first_name,' ',u.last_name),'') as requestor,DATEDIFF(now(),h.created_at) as days_ago 
                ,TIMESTAMPDIFF(HOUR,now(),h.due_date) as due_in_hours, ifnull(ts.description,'') as ticket_status, ifnull(tp.description,'') as ticket_priority
                ,concat(pc.first_name,' ',pc.last_name) as agent
                ,ifnull(pcom.company_name,'None') as merchant
                ,ifnull(assignee,'') as assignee
                ,ut.description as department_name,pr.name as product_name
                ,if(h.parent_id = -1,'Main','Sub') as type
                from ticket_headers h
                left join users u on u.username = h.create_by
                left join ticket_statuses ts on ts.code = h.status
                left join ticket_priorities tp on tp.code = h.priority
                left join partners p on p.id=h.partner_id
                left join partner_contacts pc on pc.partner_id=p.parent_id  and pc.is_original_contact=1
                left join partner_companies pcom on pcom.partner_id=p.id
                left join partner_contacts pcon on pcon.partner_id=p.id  and pcon.is_original_contact=1
                left join user_types ut on ut.id = h.department
                left join products pr on pr.id = h.product_id
                where h.id > 0 and is_deleted=0
                ";
        if (isset($params['add_query'])) {
            $cmd .= $params['add_query'];
        }

        if (isset($params['type'])) {
            if ($params['type'] == 'main') {
                $cmd .= " and h.parent_id = -1 ";
            } else {
                $cmd .= " and h.parent_id <> -1 ";
            }
        }

        if (isset($params['product_id'])) {
            $cmd .= " and pr.id={$params['product_id']} ";
        }
        if (isset($params['department_id'])) {
            $cmd .= " and ut.id={$params['department_id']} ";
        }
        if (isset($params['merchant_id'])) {
            $cmd .= " and h.partner_id={$params['merchant_id']} ";
        }
        if (isset($params['user_id'])) {
            $cmd .= " and find_in_set('{$params['user_id']}',h.assignee) <> 0 ";
        }
        if (isset($params['creation_filter_code'])) {
            $cmd .= " " . $this->get_table_field_by_value('ticket_filter_creations', 'remarks', 'code', $params['creation_filter_code']);
        }
        if (isset($params['filter_checkbox'])) {
            if (strlen($params['filter_checkbox']) > 0) {
                $filter_cb = explode(",", $params['filter_checkbox']);
                foreach ($filter_cb as $fcb) {
                    $fcb = trim($fcb);
                    $cmd .= " " . Access::get_table_field_by_value('ticket_filters', 'remarks', 'code', $fcb);
                }

            }
        }
        if (isset($params['code'])) {
            $cmd .= " " . Access::get_table_field_by_value('ticket_filters', 'remarks', 'code', $params['code']);
        }
        if (strpos($cmd, 'is_deleted=1') !== false) {
            $cmd = str_replace(" and is_deleted=0", "", $cmd);
        }
        if (strpos($cmd, 'order by') !== false) {
            $cmd .= " ,h.created_at desc";
        } else {
            $cmd .= " order by h.created_at desc";
        }


        $tickets = DB::select(DB::raw($cmd));
        $results = array();
        foreach ($tickets as $ticket) {
            if ($ticket->assignee != "") {
                $cmd = "SELECT id,concat(first_name,' ',last_name) as name FROM users where status='A' and id IN({$ticket->assignee})";
                $users = DB::select(DB::raw($cmd));
            } else {
                $users = array();
            }
            $ticket->users = $users;
            $results[] = $ticket;
        }

        return $results;
    }

}
