<?php
/**
 * Created by PhpStorm.
 * User: eunamagpantay
 * Date: 5/17/18
 * Time: 10:44 AM
 */

namespace App\Services;

use App\Contracts\Constant;
use App\Contracts\TicketService;
use App\Models\Company;
use App\Models\Partner;
use App\Models\PartnerCompany;
use App\Models\PartnerType;
use App\Models\Product;
use App\Models\TicketDetail;
use App\Models\TicketHeader;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\UserType;
use App\Services\Utility\Helper;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Object_;

class TicketServiceImpl extends BaseServiceImpl implements TicketService
{
    /**
     * List all tickets with relationships
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function listTicket()
    {
        $ticketHeaders = TicketHeader::with('ticketType', 'createdBy', 'partnerType', 'product', 'userType', 'user', 'ticketStatus', 'ticketPriority', 'product.userTypes', 'product.userTypes.users', 'partner', 'partner.partner_company')
            ->where([
                ['status', '=', TicketHeader::TICKET_STATUS_OPEN],
                ['parent_id', '=', -1]
            ])->orderBy('created_at','desc')
            ->get();

        $results = [];
        if (Auth::user()->user_type_id != 1) {
            if (isset($ticketHeaders)) {
                foreach ($ticketHeaders as $ticketHeader) {
                    $explodeAssignee = explode(',', $ticketHeader->assignee);
                    if (in_array(Auth::id(), $explodeAssignee)) {
                        $results[] = $ticketHeader;
                    }
                }
            }
        } else {
            $results = $ticketHeaders;
        }

        return $results;
    }

    /**
     * List all filtered tickets
     *
     * @param null $type
     * @param null $product
     * @param null $department
     * @param null $users
     * @param null $partner
     * @param null $created
     * @param null $dueBy
     * @param null $status
     * @return mixed
     */
    public function listFilters($type = null, $product = null, $department = null, $users = null, $partner = null, $created = null, $dueBy = null, $status = null)
    {
        $ticketHeaders = TicketHeader::with('ticketType', 'createdBy', 'partnerType', 'product', 'userType', 'user', 'ticketStatus', 'ticketPriority', 'product.userTypes', 'product.userTypes.users', 'partnerCompany')
            ->where([
                ['status', '=', $status],
                ['parent_id', '=', TicketHeader::TICKET_PARENT_ID_NEGATIVE]
            ])
            ->ofType($type)
            ->ofProduct($product)
            ->ofDepartment($department)
            ->ofAssignedTo($users)
            ->ofPartner($partner)
            ->ofCreated($this->generateCreatedData($created))
            ->ofDueBy($this->generateDueBy($dueBy))
            ->orderBy('created_at','desc')
            ->get();


        $results = [];
        if (Auth::user()->user_type_id != 1) {
            if (isset($ticketHeaders)) {
                foreach ($ticketHeaders as $ticketHeader) {
                    $explodeAssignee = explode(',', $ticketHeader->assignee);
                    if (in_array(Auth::id(), $explodeAssignee)) {
                        $results[] = $ticketHeader;
                    }
                }
            }
        } else {
            $results = $ticketHeaders;
        }


        return $results;
    }

    /**
     * Generate created data for where clause
     *
     * @param $created
     * @return null|string
     */
    protected function generateCreatedData($created)
    {
        if (isset($created)) {
            $generateCreated = Helper::generateCreated();
            $explodeCreated = explode('.', $created);
            $createdValue = $generateCreated[$explodeCreated[0]][$explodeCreated[1]];
            if (is_array($createdValue)) {
                $createdData = " ( created_at BETWEEN '" . $createdValue[0] . "' AND '" . $createdValue[1] . "' )";
            } else {
                if (strstr($createdValue, '%')) {
                    $createdData = " created_at LIKE '" . $createdValue . "'";
                } else {
                    $createdData = " created_at BETWEEN '" . $generateCreated['current']['now'] . "' AND '" . $createdValue . "'";
                }
            }
        } else {
            $createdData = null;
        }
        return $createdData;
    }

    /**
     * Ticket product information
     *
     * @param $productId
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|object|static
     */
    public function listProductInformation($productId)
    {
        $product = Product::find($productId);
        foreach($product->userTypes as $ut)
        {
            $partnerType = PartnerType::where('user_type_id',$ut->id)->first();
            if(isset($partnerType)){
                $ut->status = 'D';
            }
        }
        return $product;
    }

    /**
     * Update ticket
     *
     * @param Request $request
     * @param $id
     * @param bool $isComment
     * @return mixed
     */
    public function updateTicket(Request $request, $id, $isComment = false)
    {

        /**
         * Get ticket header entity
         */
        $ticketHeader = TicketHeader::where("id", "=", $id)->first();

        $attachments = [];

        if (!empty($ticketHeader->attachment)) {
            $attachments = json_decode($ticketHeader->attachment, true);
        }

        /**
         * File upload
         */

        if ($request->hasFile("attachment")) {
            $attachmentFile = $request->file("attachment");
            $storagePath = Storage::disk('public')->putFileAs('attachment', $attachmentFile, "ticket/" . $attachmentFile->getClientOriginalName(), 'public');
            $attachments[] = $storagePath;
        }

        $assignees = $request->get('assignee');
        /**
         * Update ticket
         */
        $data = [
            'subject' => $request->get('title'),
            'partner_id' => $request->get('partnerUser'),
            'product_id' => $request->get('product'),
            'status' => $request->get('status'),
            'type' => $request->get('type'),
            'priority' => $request->get('priority'),
            'department' => $request->get('department'),
            'due_date' => $request->get('due_date'),
            'description' => htmlentities($request->get('description')),
            'update_by' => Auth::id(),
            'assignee' => (isset($assignees) ? implode(',', $request->get('assignee')) : null),
            'pmx_flag' => null,
            'pmx_attachment' => null,
            'attachment' => json_encode($attachments)
        ];

        /**
         * Where comment is true update Ticket header and ticket details
         */
        if (!$isComment) {
            return TicketHeader::where("id", "=", $id)->update($data);
        } else {
            return DB::transaction(function () use ($data, $id) {
                /**
                 * Update ticket header
                 */
                TicketHeader::where('id', '=', $id)->update($data);

                /**
                 * Add ticket details for comments
                 */
            });
        }

    }


    /**
     * Generated where or for due by
     *
     * @param $dueByValues
     * @return string
     */
    protected function generateDueBy($dueByValues)
    {
        $generateDueByResponse = [];
        foreach ($dueByValues as $dueByValue) {
            if ($dueByValue == TicketHeader::TICKET_DUE_BY_OVERDUE) {
                $generateDueByResponse[] = " due_date <= '" . Carbon::now()->format('Y-m-d H:i:s') . "' ";
            } elseif ($dueByValue == TicketHeader::TICKET_DUE_BY_TODAY) {
                $generateDueByResponse[] = " due_date LIKE '" . Carbon::now()->format('Y-m-d') . "%' ";
            } elseif ($dueByValue == TicketHeader::TICKET_DUE_BY_TOMORROW) {
                $generateDueByResponse[] = " due_date LIKE '" . Carbon::now()->addDay(1)->format('Y-m-d') . "%' ";
            } else {
                $generateDueByResponse[] = " due_date BETWEEN '" . Carbon::now()->format('Y-m-d H:i:s') . "' AND '" . Carbon::now()->addHours(8)->format('Y-m-d H:i:s') . "' ";
            }
        }

        if (count($generateDueByResponse) > 0) {
            return '(' . implode(' OR ', $generateDueByResponse) . ') ';
        } else {
            return null;
        }
    }

    /**
     * Update ticket header status
     *
     * @param $ids
     * @param $status
     * @return mixed|void
     */
    public function updateStatus($ids, $status)
    {
        $data = [];
        $data['status'] = $status;
        /**
         * Deleted
         */
        if ($status == TicketHeader::TICKET_STATUS_DELETED) {
            $data['delete_by'] = Auth::id();
            $data['delete_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['is_deleted'] = 1;
            TicketHeader::whereIn('id', $ids)->update($data);
        } elseif ($status == TicketHeader::TICKET_STATUS_MERGE) {

            $ticket = TicketHeader::where("id", "=", $ids[0])->first();

            if (isset($ticket)) {
                /**
                 * Remove first array
                 */
                array_shift($ids);
                /**
                 * Update sub tickets
                 */

                $ticketHeaders = TicketHeader::with('product')->whereIn('id', $ids)->get();

                if (!isset($ticketHeaders)) {
                    abort(404);
                } else {
                    /**
                     * Initialized db transaction
                     */
                    DB::transaction(function () use ($ticket, $ticketHeaders) {
                        $ticketDetails = [];
                        $comments = [];
                        foreach ($ticketHeaders as $ticketHeaderKey => $ticketHeaderValue) {


                            $department = UserType::where('id', '=', $ticketHeaderValue->department)->first();
                            $priority = TicketPriority::where('code', '=', $ticketHeaderValue->priority)->first();
                            $type = TicketType::where('code', '=', $ticketHeaderValue->type)->first();
                            $status = TicketStatus::where('code', '=', $ticketHeaderValue->status)->first();
                            $partnerCompany = PartnerCompany::where('id', '=', $ticketHeaderValue->partner_id)->first();
                            $allAssignees = [];
                            if (!empty($ticketHeaderValue->assignee)) {
                                $explodedAssignees = explode(",", $ticketHeaderValue->assignee);
                                $assignees = User::whereIn('id', $explodedAssignees)->get();
                                if (isset($assignees)) {
                                    foreach ($assignees as $assignee) {
                                        $allAssignees[] = $assignee->first_name . " " . $assignee->last_name;
                                    }
                                }
                            }

                            /**
                             * Constructing of ticket information
                             */
                            $description = "<strong>Ticket #:</strong>" . $ticketHeaderValue->id . "<br />";
                            $description .= "<strong>Ticket Creator:</strong>" . $ticketHeaderValue->create_by . "<br />";
                            $description .= "<strong>Title:</strong>" . $ticketHeaderValue->subject . "<br />";
                            $description .= "<strong>Product:</strong>" . (($ticketHeaderValue->product) ? $ticketHeaderValue->product->name : "N/A" ). "<br />";
                            $description .= "<strong>Department:</strong>" . (isset($department) ? $department->description : "N/A") . "<br />";
                            $description .= "<strong>Partner:</strong>" . (isset($partnerCompany) ? $partnerCompany->company_name : "N/A") . "<br />";
                            $description .= "<strong>Type:</strong>" . (isset($type) ? $type->description : "N/A" ) . "<br />";
                            $description .= "<strong>Priority:</strong>" . (isset($priority) ? $priority->description : "N/A" ). "<br />";
                            $description .= "<strong>Status:</strong>" . (isset($status) ? $status->description : "N/A" ) . "<br />";
                            $description .= "<strong>Assignees:</strong> " . (count($allAssignees) > 0 ? implode(', ', $allAssignees) : "N/A") . "<br />";
                            $description .= "<strong>Description:</strong>" . $ticketHeaderValue->description . "<br />";

                            /**
                             * Ticket detail data
                             */
                            $ticketDetails[] = [
                                'ticket_id' => $ticket->id,
                                'message' => 'Merged ticket By ' . Auth::user()->first_name . " " . Auth::user()->last_name . " (" . Carbon::now()->format('F d, Y h:i A') . ") <br /> " . $description,
                                'create_by' => Auth::user()->id,
                                'attachment' => $ticketHeaderValue->attachment,
                                'created_at' => Carbon::now()
                            ];

                            /**
                             * Description
                             */
                            if (!empty($ticketHeaderValue->description)) {
                                $comments[] = $ticketHeaderValue->description;
                            }

                            /**
                             * Update status and parent_id
                             */
                            $ticketHeaderValue->parent_id = $ticket->id;
                            $ticketHeaderValue->status = TicketHeader::TICKET_STATUS_CLOSE;
                            $ticketHeaderValue->save();
                        }

                        /**
                         * Batch insert for ticket details
                         */
                        TicketDetail::insert($ticketDetails);

                        /**
                         * Update parent ticket description
                         */
                        $ticket->description = $ticket->description . ". " . implode('. ', $comments);
                        $ticket->save();

                    });

                }
            }
        } else {
            /**
             * Update
             */
            if (!is_array($ids)) {
                DB::transaction(function () use ($ids, $data) {
                    /**
                     * Getting ticket header, from Ticket status and to ticket status
                     */
                    $ticketHeader = TicketHeader::where('id', '=', $ids)->first();
                    $fromTicketStatus = TicketStatus::where('code', '=', $ticketHeader->status)->first();
                    $toTicketStatus = TicketStatus::where('code', '=', $data['status'])->first();
                    $ticketHeader->update_by = Auth::id();
                    $ticketHeader->status = $toTicketStatus->code;
                    $ticketHeader->save();

                    /**
                     * Add comment
                     */

                    TicketDetail::create([
                        'ticket_id' => $ids,
                        'message' => Auth::user()->first_name . " " . Auth::user()->last_name . " (" . Carbon::now()->format('F d, Y h:i A') . "): <br /> Status has been changed from <strong>" . $fromTicketStatus->description . "</strong> to <strong>" . $toTicketStatus->description . "</strong>.",
                        'create_by' => Auth::id()
                    ]);
                });
            } else {
                $data['update_by'] = Auth::id();
                TicketHeader::whereIn('id', $ids)->update($data);
            }
        }

    }

    /**
     * Show information for showing data
     *
     * @param $id
     * @return mixed|\stdClass
     */
    public function showInformation($id)
    {
        $ticketHeader = TicketHeader::with('product', 'partnerCompany')->with(['ticketDetails' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where("id", "=", $id)->first();

        if (!isset($ticketHeader)) {
            abort(404);
        }

        /**
         * Detail object
         */
        $detail = new \stdClass();
        $detail->ticketHeader = $ticketHeader;
        $detail->product = $ticketHeader->product;
        $detail->department = UserType::where('id', '=', $ticketHeader->department)->first();
        $detail->priority = TicketPriority::where('code', '=', $ticketHeader->priority)->first();
        $detail->type = TicketType::where('code', '=', $ticketHeader->type)->first();
        $detail->status = TicketStatus::where('code', '=', $ticketHeader->status)->first();
        $detail->partnerCompany = PartnerCompany::where('id', '=', $ticketHeader->partner_id)->first();
        if (!empty($ticketHeader->assignee)) {
            $explodedAssignees = explode(",", $ticketHeader->assignee);
            $detail->users = User::whereIn('id', $explodedAssignees)->get();
        }


        /**
         * Returning detailed object
         */
        return $detail;
    }

    /**
     * Adding comment for specific tickets
     *
     * @param Request $request
     * @param $ticketId
     * @return mixed|string
     */
    public function addingComment(Request $request, $ticketId)
    {
        /**
         * File upload
         */
        $storagePath = [];
        if ($request->hasFile("attachment")) {
            $attachmentFile = $request->file("attachment");
            $storagePath[] = Storage::disk('public')->putFileAs('ticket', $attachmentFile, $ticketId . "/attachment/" . time() . "." . $attachmentFile->getClientOriginalExtension(), 'public');
        }

        /**
         * Comment or message reconstruction
         */
        $message = "Comment by: " . Auth::user()->first_name . " " . substr(Auth::user()->last_name, 0, 1) . ".";
        $message .= " (" . Carbon::now()->format('F d, Y h:i A') . ") ";
        $message .= htmlentities($request->get('comment'));

        /**
         * Ticket detail creation
         */
        try {
            TicketDetail::create([
                'ticket_id' => $ticketId,
                'message' => $message,
                'attachment' => \json_encode($storagePath),
                'create_by' => Auth::id()
            ]);
            return TicketHeader::TICKET_TYPE_SUCCESS;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get all companies
     *
     * @param $partnerTypeId
     * @return mixed|void
     */
    public function getCompanies($partnerTypeId,$partnerIds)
    {
        $companies = PartnerCompany::with("partner")->whereHas("partner", function ($query) use ($partnerTypeId,$partnerIds) {
            if($partnerIds == ""){
                $query->where("partner_type_id", "=", $partnerTypeId);
            }else{
                $query->where("partner_type_id", "=", $partnerTypeId)->whereRaw("partner_id in(".$partnerIds.")");
            }
        })->get();

        return $companies;
    }

    /**
     * Returns assignee of ticket
     *
     * @param $assignee
     * @return mixed
     */
    public function getTicketAssignees($assignee)
    {
        $assigneeArray = explode(',', $assignee);

        $users = User::whereIn('id', $assigneeArray)->get();

        return $users;
    }

    /**
     * List tickets from quick filter
     *
     * @param null $type
     * @param null $condition
     * @param string $value
     * @param string $owner
     * @param string $isSorting
     * @return array|\Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function listQuickFilter($type = null, $condition = null, $value = "", $owner = "", $isSorting = "")
    {
        $returnTickets = [];
        $ticketHeaders = TicketHeader::with('ticketType', 'createdBy', 'partnerType', 'product', 'userType', 'user', 'ticketStatus', 'ticketPriority', 'product.userTypes', 'product.userTypes.users', 'partner', 'partner.partner_company');
        if ($type == "status" || $type == "assignee" || $type == "create_by") {
            if ($type == "create_by") {
                $value = Auth::user()->username;
            }
            $ticketHeaders->where($type, $condition, $value)->orderBy('created_at', 'DESC');
        }

        $ticketHeaders->where("parent_id", "=", -1);

        /**
         * If looking for new tickets
         */
        if ($condition == "DESC") {
            if ($type == "created_at") {
                $ticketHeaders->where('updated_at', '=', null)->orderBy($type, $condition);
            }
        }

        /**
         * For users
         */
        if ($owner == "user") {
            $returnTickets = $ticketHeaders
                ->whereRaw('FIND_IN_SET(' . Auth::id() . ',assignee)')
                ->get();

        } elseif ($owner == "group") {
            /**
             * Group needs to have departments
             */


            if ($type == "updated_at") {
                $ticketHeaders->where('updated_at', '!=', 'created_at');
            }

            $userDepartments = explode(',', Auth::user()->user_type_id);
            //$userDepartments[] = 21;
            $tickets = $ticketHeaders->whereIn("department", $userDepartments);
            $returnTickets = $tickets->get();

        } else {
            /**
             * Default
             */
            $returnTickets = $ticketHeaders->get();
        }

        return $returnTickets;
    }


}