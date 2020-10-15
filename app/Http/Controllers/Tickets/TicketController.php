<?php

namespace App\Http\Controllers\Tickets;


use App\Contracts\TicketActionService;
use App\Contracts\TicketCountService;
use App\Contracts\TicketListService;
use App\Contracts\TicketNotifyService;
use App\Contracts\Tickets\TicketActivityListService;
use App\Contracts\Users\UserListService;
use App\Http\Controllers\Admin\BaseController;
use App\Http\Resources\TicketDetailResource;
use App\Http\Requests\Tickets\CreateTicketRequest;
use App\Http\Requests\Tickets\EditTicketRequest;
use App\Models\Access;
use App\Models\Partner;
use App\Models\Product;
use App\Models\TicketCC;
use App\Models\TicketDetail;
use App\Models\TicketHeader;
use App\Models\TicketPriority;
use App\Models\TicketReason;
use App\Models\TicketType as TicketIssueType;
use App\Models\TicketStatus;
use App\Models\TicketAttachment;
use App\Models\TicketDetailsAttachment;
use App\Models\TicketReplyTemplate;
use App\Models\User;
use App\Models\UserType;
use App\Services\Tickets\TicketAccessClassification;
use App\Services\Tickets\TicketDependencies;
use App\Services\Tickets\TicketUserClassification;
use App\Services\Tickets\Requesters\TicketRequesterAccessor;
use App\Services\Tickets\Users\TicketUserAccessorFactory;
use App\Services\Workflow\TicketGenerator;
use App\Services\Products\ProductAccessor;
use App\Services\Users\UserClassification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mail;
use Webklex\IMAP\Facades\Client;
use Yajra\Datatables\Datatables;


class TicketController extends BaseController
{
    protected $talService; /** tal = ticketActivityList */
    protected $ticketActionService;
    protected $ticketCountService;
    protected $ticketListService;
    protected $ticketNotifyService;
    protected $userListService;

    public function __construct(
        TicketActionService $ticketActionService, 
        TicketActivityListService $talService,
        TicketCountService $ticketCountService, 
        TicketListService $ticketListService,
        TicketNotifyService $ticketNotifyService,
        UserListService $userListService)
    {
        $this->talService = $talService;
        $this->ticketActionService = $ticketActionService;
        $this->ticketCountService = $ticketCountService;
        $this->ticketListService = $ticketListService;
        $this->ticketNotifyService = $ticketNotifyService;
        $this->userListService = $userListService;
    }

    public function index()
    {
        $user = User::find(Auth::id());
        $userClassification = new UserClassification($user);

        if ($userClassification->isPartner || $userClassification->isMerchant)
            return redirect('tickets/adminTicket');

        return redirect('tickets/adminInternal');
    }

    public function adminTicket() 
    {   
        $ticketPriorities = TicketPriority::all();
        $ticketStatuses = TicketStatus::all();

        $allowedActions = array();
        $allowedActions['delete'] = Access::hasPageAccess('ticketing', 'delete', true);

        return view('tickets.adminTicket')->with( compact('ticketPriorities', 'allowedActions', 'ticketStatuses') );
    }

    public function adminInternal()
    {
        $userTypeIds = explode(',', auth()->user()->user_type_id);

        /** Count Tickets */
        $ticketsCount = $this->ticketCountService->countTickets();

        /** Get User Type */
        $userType = 'USER';
        if (Access::hasPageAccess('admin', 'super admin access', true) ||
            Access::hasPageAccess('admin', 'owner', true)){
            $userType = 'USER_WITH_SUPER_ADMIN_ACCESS';
            $onlineUsers = User::getOnlineUsers();
        } else {
            if (Access::hasPageAccess('ticketing', 'assign', true))
                $userType = 'USER_WITH_ASSIGN_ACCESS';
            else {
                foreach ($userTypeIds as $id) {
                    $ut = UserType::find($id);
                    if ($ut->head_id == auth()->user()->id) {
                        $userType = 'USER_DEPARTMENT_HEAD';
                    }
                }
            }
            $onlineUsers = User::getOnlineUsers(auth()->user()->company_id);
            $companies = null;
        }

        $companies = Partner::with(['departments' => function($query) {
            $query->with(['users' => function($query) {
                        $query->orderBy('first_name')
                            ->orderBy('last_name');
                    }])
                    ->with('ticketHeaders')
                    ->orderBy('description');
            }])
            ->with('partner_company')
            ->where('partner_type_id', '7')
            ->whereCompany(auth()->user()->company_id)
            ->get()
            ->sortBy(function($company) { 
                return $company->partner_company->company_name;
            });

        /** Check if User has rights to Assign */
        $allowedActions = array();
        $allowedActions['assign'] = Access::hasPageAccess('ticketing', 'assign', true);
        $allowedActions['merge'] = Access::hasPageAccess('ticketing', 'merge', true);
        $allowedActions['delete'] = Access::hasPageAccess('ticketing', 'delete', true);

        $allDepartments = null;
        if (Access::hasPageAccess('admin', 'super admin access', true)) {
            $allDepartments = UserType::where('status', 'A')->get();
        } else if (Access::hasPageAccess('ticketing', 'assign', true)) {
            $allDepartments = UserType::where('status', 'A')
                ->where('company_id', auth()->user()->company_id)
                ->get();    
        } else {
            $userTypeIds = explode(',' , auth()->user()->user_type_id);
            $allDepartments = UserType::where('status', 'A')
                ->whereIn('id', $userTypeIds)
                ->get();
        }
            
        /** Check if System User */
        $isSystemUser = true;
        foreach ($userTypeIds as $id) {
            $ut = UserType::find($id);

            if ($ut->create_by != 'SYSTEM') {
                $isSystemUser = false;
                break;
            }
        }

        $ticketPriorities = TicketPriority::all();
        $ticketStatuses = TicketStatus::all();

        $requesterGroups = $this->userListService->getRequesterUsersByCompany(
            auth()->user()->company_id);

        return view('tickets.internalTicket')->with( 
            compact(
                'ticketsCount',
                'userType',
                'allowedActions',
                'ticketPriorities',
                'ticketStatuses',
                'allDepartments',
                'isSystemUser',
                'companies',
                'onlineUsers',
                'requesterGroups'
            )
        );
    }

    public function getPartnerOrMerchantTickets()
    {
        $userId = auth()->user()->id;
        $userTypeIds = explode(',', auth()->user()->user_type_id);

        $filterCode = substr(request()->filter, 0, 1);
        $statusCode = strlen(request()->filter) == 1 ? null : substr(request()->filter, 1);

        $priorityCode = request()->priorityCode;
        $priorityCode = $priorityCode == 'A' ? null : $priorityCode;

        $tickets = $this->ticketListService->listPartnerOrMerchantTickets($statusCode, 
            $priorityCode);

        return $this->ticketListService->formatTicketsForDatatable($tickets);
    }

    public function getInternalTickets()
    {
        $userId = auth()->user()->id;
        $userTypeIds = explode(',', auth()->user()->user_type_id);

        $filterCode = substr(request()->filter, 0, 1);
        $statusCode = strlen(request()->filter) == 1 ? null : substr(request()->filter, 1);

        $departmentIds = array();
        $departmentId = request()->departmentId;
        switch ($departmentId) {
            case 'A':
                $c1 = $filterCode == 'A';
                $c2 = !Access::hasPageAccess('ticketing', 'assign', true);
                if ($c1 && $c2) {
                    $departmentIds = $userTypeIds;
                    break;
                }

                $departmentIds = null;
                break;

            case 'AD':
                $departmentIds = [];
                break;

            case 'N':
                $departmentIds[] = -1;
                break;

            case 'M':
                $departmentIds = $userTypeIds;
                break;
            
            default:
                $departmentIds[] = $departmentId;
        }

        $priorityCode = request()->priorityCode;
        $priorityCode = $priorityCode == 'A' ? null : $priorityCode;

        if (isset(request()->companyId) && request()->companyId != null) {
            $companyId = request()->companyId;

            if ($companyId == 'A') {
                $companyId = auth()->user()->company_id;
            }
            
        } else {
            $companyId = auth()->user()->company_id;
        }

        $requesterId = request()->requesterId;
        $tickets = $this->ticketListService->listInternalTickets($filterCode, 
            $statusCode, $departmentIds, $priorityCode, $companyId, $requesterId);
        return $this->ticketListService->formatTicketsForDatatable($tickets);
    }

    public function assignToMeTickets(Request $request)
    {
        $resultObject = $this->ticketActionService->assignToMeTickets($request->ticket_ids);

        $this->ticketNotifyService->notifyOnAction(
            $resultObject->ticketHeaders, 'assign');
        
        $this->ticketNotifyService->notifyOnActionThroughEmail(
            $resultObject->ticketHeaders, 'assign');

        return response()->json([
            'assignedTicketIds' => $resultObject->assignedTicketIds,
            'unprocessedTicketIds' => $resultObject->unprocessedTicketIds
        ], 200);
    }

    public function assignTickets(Request $request)
    {
        $resultObject = $this->ticketActionService->assignTickets(
            $request->ticket_ids, 
            $request->department_id, 
            $request->assignee_id
        );

        $this->ticketNotifyService->notifyOnAction($resultObject->ticketHeaders, 'assign');
        $this->ticketNotifyService->notifyOnActionThroughEmail($resultObject->ticketHeaders, 'assign');

        return response()->json([
            'assignedTicketIds' => $resultObject->assignedTicketIds,
            'unprocessedTicketIds' => $resultObject->unprocessedTicketIds
        ], 200);
    }

    public function deleteTickets(Request $request)
    {
        $resultObject =  $this->ticketActionService->deleteTickets(
            $request->ticket_ids);
        
        $this->ticketNotifyService->notifyOnAction(
            $resultObject->ticketHeaders, 'delete');

        $this->ticketNotifyService->notifyOnActionThroughEmail(
            $resultObject->ticketHeaders, 'delete');

        return response()->json([
            'deletedTicketIds' => $resultObject->deletedTicketIds,
            'unprocessedTicketIds' => $resultObject->unprocessedTicketIds
        ], 200);
    }

    public function mergeTickets(Request $request)
    {
        $resultObject = $this->ticketActionService->mergeTickets(
            $request->ticket_ids);

        if (!$resultObject->success) {
            return response()->json([
                'success' => false,
            ], 200);
        }

        $this->ticketNotifyService->notifyOnAction(
            $resultObject->ticketHeaders, 'merge', $request->ticket_ids);

        $this->ticketNotifyService->notifyOnActionThroughEmail(
            $resultObject->ticketHeaders, 'merge', $request->ticket_ids);
        
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function create()
    {
        $user = User::find(Auth::id());
        $userClassification = new UserClassification($user);
        $productAccessor = new ProductAccessor($user);

        return view('tickets.create')->with([
            'productsGroups' => $productAccessor->productsGroups,
            'ticketPriorities' => TicketPriority::all(),
            'userClassification' => $userClassification,
        ]);
    }

    public function createDependencies($productId)
    {
        $product = Product::withTicketAssignees()->findOrFail($productId);
        $user = User::find(Auth::id());

        $ticketIssueTypes = TicketIssueType::select('id', 'description')
            ->isActive()
            ->where('company_id', $product->company_id)
            ->get();

        $ticketReasons = TicketReason::select(
                'id', 
                'description', 
                'department_id',
                'ticket_type_id', 
                'ticket_priority_code')
            ->isActive()
            ->where('company_id', $product->company_id)
            ->get();

        $ticketRequesterAccessor = new TicketRequesterAccessor(
            $product, 
            $user);

        $ticketUserClassification = new TicketUserClassification($user);
        $userClassification = $ticketUserClassification->getClassification();
        $usersGroups = (new TicketUserAccessorFactory())
            ->make($user, $userClassification)
            ->getUsers()
            ->groupBy('company_id');


        return response()->json([
            'departmentsGroups' => $product->userTypes->groupBy('company_id'),
            'issueTypes' => $ticketIssueTypes,
            'reasons' => $ticketReasons,
            'merchants' => $ticketRequesterAccessor->merchantsGroups,
            'partners' => $ticketRequesterAccessor->partnersGroups,
            'companyId' => $product->company_id,
            'usersGroups' => $usersGroups,
        ], 200);
    }

    public function storeAjax(CreateTicketRequest $request)
    {
        DB::transaction(function() use ($request) {
            $companyId = UserType::find($request->assignee_department)->company_id;
            $ticketHeader = TicketHeader::create([
                'product_id' => $request->product,
                'subject' => $request->subject,
                'description' => $request->description,
                'due_date' => $request->due_date,
    
                'status' => $request->status,
                'priority' => $request->priority,
                'type' => $request->issue_type,
                'reason' => $request->reason,
    
                'requester_id' => $request->requester_id,
                'assignee' => $request->assignee_user,
                'department' => $request->assignee_department,
                'company_id' => $companyId,
    
                'sub_task_detail_id' => null,
                'responsed_at_department' => null,
                'responsed_at_assignee' => null,
    
                'create_by' => Auth::user()->username,
                'update_by' => Auth::user()->username
            ]);

            if ($request->hasFile("attachments")) {
                $attachments = $request->file('attachments');
    
                foreach ($attachments as $attachment) {
                    $filename = pathinfo(
                        $attachment->getClientOriginalName(), 
                        PATHINFO_FILENAME);

                    $extension = $attachment->getClientOriginalExtension();
    
                    $filenameToStore  = str_replace(' ', '', $filename) . '_';
                    $filenameToStore .= time() . '.' . $extension;
                    $filenameToStore  = str_replace('#', '', $filenameToStore);
    
                    $storagePath = Storage::disk('public')->putFileAs(
                        "attachment", 
                        $attachment, 
                        "ticket/" . $filenameToStore, 
                        "public");
    
                    $ticketAttachment = new TicketAttachment;
                    $ticketAttachment->name = $attachment->getClientOriginalName();
                    $ticketAttachment->path = $filenameToStore;
                    $ticketAttachment->ticket_header_id = $ticketHeader->id;
                    $ticketAttachment->save();
                }
            }
    
            if (isset($request->ccs)) {
                foreach ($request->ccs as $ccId) {
                    $ticketCC = New TicketCC;
                    $ticketCC->user_id = $ccId;
                    $ticketCC->ticket_header_id = $ticketHeader->id;
                    $ticketCC->save();
                } 
            }
    
            $this->ticketNotifyService->notifyOnCreateThroughEmail($ticketHeader);
            $this->ticketNotifyService->notifyOnCreate($ticketHeader); 
        });
        
        return response()->json(null, 200);
    }

    public function edit($id)
    {
        $ticketHeader = TicketHeader::with('ticketDetails.attachments')
            ->with('ccs')
            ->with('attachments')
            ->with('subTaskDetail')
            ->with('assignedTo')
            ->with('requester')
            ->with('requester.partner')
            ->with('createdBy')
            ->withoutGlobalScopes()
            ->findOrFail($id);

        $user = User::find(Auth::id());
        $product = Product::withTicketAssignees()->findOrFail($ticketHeader->product_id);

        $ticketRequesterAccessor = new TicketRequesterAccessor(
            $product, 
            $user);

        $ticketUserClassification = new TicketUserClassification($user);
        $userClassification = $ticketUserClassification->getClassification();

        $ticketDependencies = new TicketDependencies(
            $user,
            $userClassification,
            $ticketHeader);

        $ticketAccessClassification = new TicketAccessClassification(
            $user,
            $userClassification,
            $ticketHeader);

        $ticketActivities = $this->talService->getTicketActivitiesByTicketHeaderId($ticketHeader->id);
        if (! $product->userTypes->contains($ticketHeader->userType)) {
            $userType = $ticketHeader->userType;
            $userType->load('partnerCompany:id,partner_id,company_name');
            $userType->load(['users' => function($query) {
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
                }]);
                
            $product->userTypes->push($userType);
        }

        return view('tickets.edit')->with([
            'departments' => $product->userTypes,
            'departmentsGroups' => $product->userTypes->groupBy('company_id'),
            'ticketAccessClassification' => $ticketAccessClassification,
            'ticketActivities' => $ticketActivities,
            'ticketDependencies' => $ticketDependencies,
            'ticketUserClassification' => $ticketUserClassification,
            'ticketHeader' => $ticketHeader,
            'ticketRequesterAccessor' => $ticketRequesterAccessor,

            'isWorkflowTicket' => isset($ticketHeader->sub_task_detail_id),
            'allPrivileges' => $ticketAccessClassification->all,
            'replyOnly' => $ticketAccessClassification->replyOnly,
            'viewOnly' => $ticketAccessClassification->viewOnly,
        ]);
    }

    public function createTicketsViaEmail()
    {
        $oClient = Client::account('default');
        $oClient->connect();
        $aFolder = $oClient->getFolders();
        $oFolder = $oClient->getFolder('INBOX');
        // $aMessage = $oFolder->query()->unseen()->get();  
        $aMessage = $oFolder->query()->all()->get(); 
        $preview = array();
        foreach ($aMessage as $msg) {
            $user = User::where('email_address',$msg->getFrom()[0]->mail)->first();
            if(isset($user)){
                if($msg->getReferences()  !== null){
                    $references= explode(' ', $msg->getReferences());
                    $reference = str_replace('<', '', $references[0]);
                    $reference = str_replace('>', '', $reference);
                    $ticket = TicketHeader::where('email_message_id',$reference)->first();
                    if(isset($ticket)){
                        $ticketDetail = new TicketDetail;
                        $ticketDetail->ticket_id = $ticket->id;
                        $ticketDetail->message = $msg->getHTMLBody();
                        $ticketDetail->create_by = $user->id;
                        $ticketDetail->email_message_id = $msg->getMessageId();
                        $ticketDetail->save();
                        $msg->moveToFolder('Tickets');

                       $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticketDetail) {
                            $filename = $msg->getMessageId().$oAttachment->name;
                            $filename = str_replace("#", "", $filename);
                            $oAttachment->save(storage_path('app/public/attachment/ticket'),$msg->getMessageId().$oAttachment->name);
                            $attach = new TicketDetailsAttachment;
                            $attach->ticket_detail_id = $ticketDetail->id;
                            $attach->name = $oAttachment->name;
                            $attach->path = "/attachment/ticket/". $filename;
                            $attach->save();
                       });

                    }else{
                        $ticket = TicketDetail::where('email_message_id',$reference)->first();
                        if(isset($ticket)){
                            
                            $ticketDetail = new TicketDetail;
                            $ticketDetail->ticket_id = $ticket->ticket_id;
                            $ticketDetail->message = $msg->getHTMLBody();
                            $ticketDetail->create_by = $user->id;
                            $ticketDetail->email_message_id = $msg->getMessageId();
                            $ticketDetail->save();
                            $msg->moveToFolder('Tickets');

                           $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticketDetail) {
                                $filename = $msg->getMessageId().$oAttachment->name;
                                $oAttachment->save(storage_path('app/public/attachment/ticket'),$msg->getMessageId().$oAttachment->name);
                                $attach = new TicketDetailsAttachment;
                                $attach->ticket_detail_id = $ticketDetail->id;
                                $attach->name = $oAttachment->name;
                                $attach->path = "/attachment/ticket/". $filename;
                                $attach->save();
                           });
                        }else{                        
                            $msg->moveToFolder('Ticket-Errors');
                        }
                    }

                }else{
                    $ticket = new TicketHeader;
                    $ticket->subject = $msg->getSubject();
                    $ticket->status = 1;
                    $ticket->type = 2;
                    $ticket->priority = 1;
                    $ticket->ticket_date = Carbon::now();
                    $ticket->description = $msg->getHTMLBody();
                    $ticket->create_by = $user->id;
                    $ticket->requester_id = $user->id;
                    $ticket->source_email = $msg->getFrom()[0]->mail;
                    $ticket->email_message_id = $msg->getMessageId();
                    $ticket->save();
                    $msg->moveToFolder('Tickets');

                    $data = array(
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $msg->getFrom()[0]->mail,
                        'msg' => $msg->getHTMLBody()
                    );

                    $message_id = "";
                    $response = Mail::send(['html'=>'mails.ticketaccept'],$data,function($message) use ($data,&$message_id){
                        $message->to($data['email'],$data['first_name'].' '.$data['last_name']);
                        $message->subject('[GoETU] Ticket Confirmation');
                        $message->from('no-reply@goetu.com');
                        $message_id = $message->getId();
                    });
                    $ticket->email_message_id = $message_id;
                    $ticket->save();

                   $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticket) {
                        $filename = $msg->getMessageId().$oAttachment->name;
                        $filename = str_replace("#", "", $filename);
                        $oAttachment->save(storage_path('app/public/attachment/ticket'),$filename);
                        $attach = new TicketAttachment;
                        $attach->ticket_header_id = $ticket->id;
                        $attach->name = $oAttachment->name;
                        $attach->path = "/attachment/ticket/". $filename;
                        $attach->save();
                   });

                }
            }else{
                $data = array(
                    'email' => $msg->getFrom()[0]->mail,
                    'msg' => $msg->getHTMLBody()
                );

                $message_id = "";
                $response = Mail::send(['html'=>'mails.ticketfailed'],$data,function($message) use ($data){
                    $message->to($data['email'],'');
                    $message->subject('[GoETU] Invalid Email');
                    $message->from('no-reply@goetu.com');
                });

                $msg->moveToFolder('Ticket-Errors');
            }

           $preview[] = array(
                'message_id' => $msg->getMessageId(),
                'attachment' => $msg->getAttachments(),
                'references' => $msg->getReferences(),
                'email' => $msg->getFrom()[0]->mail,
                'subject' => $msg->getSubject(),
                'message' => $msg->getHTMLBody()
                );
            
        }
        dd($preview);
    }

    public function submitTicketReply(
        EditTicketRequest $request, 
        int $ticketId, 
        string $ticketStatusCode)
    {
        DB::transaction(function() use ($request, $ticketId, $ticketStatusCode) {
            $ticketHeader = TicketHeader::find($ticketId);
            $companyId = UserType::find($request->department ?? $ticketHeader->department)->company_id;
            $ticketHeader->update([
                'department' => $request->department ?? $ticketHeader->department,
                'assignee' => $request->assignee ?? $ticketHeader->assignee,
                'requester_id' => $request->requester_id ?? $ticketHeader->requester_id,
                'product_id' => $request->product ?? $ticketHeader->product_id,
                'type' => $request->ticket_type_code ?? $ticketHeader->type,
                'reason' => $request->ticket_reason_code ?? $ticketHeader->reason,
                'due_date' => $request->due_date ?? $ticketHeader->due_date,
                'company_id' => $companyId,
                'status' => $ticketStatusCode == 'undefined' ? $ticketHeader->status : $ticketStatusCode,
                'update_by' => auth()->user()->username,
            ]);

            $ticketDetail = TicketDetail::create([
                'is_internal' => $request->is_internal_note,
                'message' => $request->message,
                'ticket_id' => $ticketHeader->id,
                'create_by' => Auth::user()->username,
                'update_by' => Auth::user()->username
            ]);
            
            if ($request->hasFile("attachments")) {
                foreach ($request->file('attachments') as $attachment) {
                    $fileName = pathinfo(
                        $attachment->getClientOriginalName(),
                        PATHINFO_FILENAME);

                    $extension = $attachment->getClientOriginalExtension();
                    $filenameToStore  = str_replace(" ", "", $fileName) . "_";
                    $filenameToStore .= time() . ".{$extension}";
                    $filenameToStore = str_replace("#", "", $filenameToStore);
                    $storagePath = Storage::disk('public')->putFileAs(
                        "attachment", 
                        $attachment, 
                        "ticket/{$filenameToStore}", 
                        "public");
    
                    $ticketAttachment = new TicketDetailsAttachment;
                    $ticketAttachment->name = $attachment->getClientOriginalName();
                    $ticketAttachment->path = $filenameToStore;
                    $ticketAttachment->ticket_detail_id = $ticketDetail->id;
                    $ticketAttachment->save();
                }
            }

            if (isset($request->cc_ids)) {
                TicketCC::where('ticket_header_id', $ticketHeader->id)->delete();
                foreach ($request->cc_ids as $cc) {
                    $ticketCC = new TicketCC;
                    $ticketCC->user_id = $cc;
                    $ticketCC->ticket_header_id = $ticketHeader->id;
                    $ticketCC->save();
                } 
            }

            $this->ticketNotifyService->notifyOnActionThroughEmail([$ticketHeader], 'reply');
            $this->ticketNotifyService->notifyOnAction([$ticketHeader], 'reply');

            if (isset($ticketHeader->subtask) && $ticketStatusCode == TicketStatus::SOLVED) {
                $ticketGenerator = new TicketGenerator($ticketHeader->subtask->task);
                $ticketGenerator->generateTickets();

                $ticketHeader->subtask->status = 'C';
                $ticketHeader->subtask->save();
            }
        });

        return response()->json(null, 200);
    }

    public function getDepartments(Request $request)
    {
        $ticketIds = explode(',', $request->ticket_ids);
        $productIds = TicketHeader::distinct()->whereIn('id', $ticketIds)->pluck('product_id')->all();

        $products = Product::withTicketAssignees()
            ->where('id', $productIds)
            ->get();

        $firstRun = true;
        $departments = $products->first()->userTypes;
        foreach ($products as $product) {
            if ($firstRun)
                continue;

            $departments = $departments->intersect($product->userTypes);
        }

        $departments = $departments->map(function($department) {
            $department->description = $department->description . " ({$department->partnerCompany->company_name})";
            return $department;
        });
        
        return response()->json([
            'departments' => $departments
        ], 200);
    }

    public function getUsersByDepartment($id)
    {
        $userType = UserType::find($id);
        return response()->json(array(
            'users' => $userType->users,
        ));
    }

    public function listReplyTemplate(){
        return view('tickets.listTemplate');
    }

    public function getReplyTemplateData(Datatables $datatables)
    {
        $query = TicketReplyTemplate::where('status','A')->get();
        return $datatables->collection($query)
                          ->editColumn('name', function ($data) {
                              return  $data->name;
                          })
                          ->editColumn('action', function ($data) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $message="'Delete this Reply Template?'";
                                $edit = '<a href="/tickets/reply-template/'.$data->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                $delete = '<a href="/tickets/reply-template/'.$data->id.'/delete" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                return $edit.' '.$delete;
                          })
                          ->rawColumns(['name','action'])
                          ->make(true);
    }

    public function createTemplateData(){
        $formUrl = "/tickets/reply-template-store";
        $headername = "Create Reply Template";        
        return view('tickets.replyTemplate',compact('formUrl','headername'));
    }

    public function storeTemplateData(Request $request){
      $validatedData = $request->validate([
            'emailTemplateTitle' => 'required',
            'emailContent' => 'required'
        ]);

      $wEmail = new TicketReplyTemplate;
      $wEmail->name = $request->emailTemplateTitle;
      $wEmail->value = $request->emailContent;
      $wEmail->status = 'A';
      $wEmail->create_by = auth()->user()->username;
      $wEmail->update_by = auth()->user()->username;
      $wEmail->save();

      return redirect('/tickets/reply-template')->with('success','Ticket Reply Template added');
    }

    public function editReplyTemplate($id){
        $data = TicketReplyTemplate::find($id);
        $formUrl = "/tickets/reply-template/update/".$id;
        $headername = "Edit Reply Template";
        return view("tickets.replyTemplate",compact('data','formUrl','headername'));
    }

    public function updateReplyTemplate(Request $request,$id){
      $validatedData = $request->validate([
            'emailTemplateTitle' => 'required',
            'emailContent' => 'required'
        ]);

      $wEmail = TicketReplyTemplate::find($id);
      $wEmail->name = $request->emailTemplateTitle;
      $wEmail->value = $request->emailContent;
      $wEmail->status = 'A';
      $wEmail->update_by = auth()->user()->username;
      $wEmail->save();

      return redirect('/tickets/reply-template')->with('success','Ticket Reply Template updated');
    }

    public function deleteReplyTemplate($id){

      $wEmail = TicketReplyTemplate::find($id);
      $wEmail->status = 'D';
      $wEmail->update_by = auth()->user()->username;
      $wEmail->save();

      return redirect('/tickets/reply-template')->with('success','Ticket Reply Template deleted');
    }

    public function getTicketReplies() 
    {
        $ticketId = request()->ticket_id;

        $ticketDetails = TicketDetail::with('attachments', 'createdBy')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'DESC')
            ->paginate(4);

        return TicketDetailResource::collection($ticketDetails);
    }

    public function getReplyTemplate($id) 
    {
        $replyTemplate = TicketReplyTemplate::find($id);

        return response()->json([
            'replyTemplateDescription' => $replyTemplate->value,
        ], 200);
    }
}