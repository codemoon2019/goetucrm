<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductOrderCommentCollection;
use App\Http\Resources\ProductOrderCommentAssignee;
use App\Models\Access;
use App\Models\EmailOnQueue;
use App\Models\ProductOrder;
use App\Models\ProductOrderComment;
use App\Models\SubTaskDetail;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductOrderCommentController extends Controller
{
    public function index($productOrderId, $subTaskDetailId)
    {
        /** @todo Check if Request Ajax */

        $productOrderComments = ProductOrderComment::with('viewers')
            ->with('viewers.department:id,description')
            ->with('productOrderCommentAttachments')
            ->withCount(['children' => function($query) {
                $query->whereHas('viewers', function($query) {
                    $isSuperAdmin = Access::hasPageAccess('admin', 'super admin access', true);
                    $isOwner = Access::hasPageAccess('admin', 'owner', true);
            
                    if ( !($isSuperAdmin || $isOwner) ) {
                        $query->where('user_id', auth()->user()->id);
                    }
                });
            }])
            ->whereHas('viewers', function($query) {
                $isSuperAdmin = Access::hasPageAccess('admin', 'super admin access', true);
                $isOwner = Access::hasPageAccess('admin', 'owner', true);
        
                if ( !($isSuperAdmin || $isOwner) ) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->where('product_order_id', $productOrderId)
            ->where('sub_task_detail_id', $subTaskDetailId)
            ->where('parent_id', isset( request()->productOrderCommentId ) ?
                request()->productOrderCommentId : null)
            ->where('status', 'A')
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        return ProductOrderCommentCollection::collection($productOrderComments);
    }

    public function store(Request $request, $productOrderId, $subTaskDetailId)
    {
        if ($request->ajax()) {

            $validation = Validator::make($request->all(), [
                'comment' => 'required',
                'viewers' => 'required',
                'attachments.*' => 'nullable|file|max:5000',
            ]);

            if ( $validation->fails() ) {
                return response($validation->errors(), 400);
            }

            DB::beginTransaction();
            try {

                $productOrder = ProductOrder::find($productOrderId);
                $productOrderComment = ProductOrderComment::create([
                    'product_order_id' => $productOrderId,
                    'parent_id' => isset($request->parent_id) ? $request->parent_id : null,
                    'product_id' => $productOrder->product_id,
                    'partner_id' => $productOrder->partner_id,
                    'comment' => $request->comment,
                    'create_by' => auth()->user()->username,
                    'user_id' => auth()->user()->id,
                    'status' => 'A',
                    'comment_status' => $request->order_status == 'N' ? null : $request->order_status,
                    'sub_task_detail_id' => $subTaskDetailId,
                    'attachment_access_ids' => isset($request->attachmentViewers) ? 
                        json_encode($request->attachmentViewers ) :
                        null
                ]);
    
                $productOrder->product_status = $request->txtOrderStatus;
                $productOrder->save();
    
                if ($request->hasFile('attachments')) {
                    $productOrderCommentAttachments = [];
                    foreach ($request->file('attachments') as $attachment) {
                        $fileNameWithExtension = $attachment->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
                        $extension = $attachment->getClientOriginalExtension();
                        $fileNameToStore = str_replace(' ', '', $fileName) . '_' . time() . '.' . $extension;
        
                        $attachment->storeAs('merchant_attachments', $fileNameToStore);
        
                        $productOrderCommentAttachments[] = [
                            'name' => $fileNameWithExtension,
                            'path' => "merchant_attachments/{$fileNameToStore}",
                            'product_order_comment_id' => $productOrderComment->id,
                        ];
                    }
                    
                    $productOrderComment->productOrderCommentAttachments()->createMany(
                        $productOrderCommentAttachments
                    );
                }

                $productOrderComment->viewers()->attach($request->viewers);
                $productOrderComment->viewers()->attach(auth()->user()->id);

                $productOrderComment->load('viewers');
                $productOrderComment->load('viewers.department');

                $this->createEmailOnQueue( $productOrderComment );

                DB::commit();
                return response(new ProductOrderCommentCollection(
                    $productOrderComment), 201);

            } catch (\Exception $ex) {
                DB::rollBack();

                return response([
                    'error' => $ex->getMessage()
                ], 500);
            }
        }
    }

    public function indexViewers($productOrderId, $subTaskDetailId)
    {
        /** @todo Check if Request Ajax */
        /** @todo Change to Eloquent */
        
        if (isset(request()->comment_id)) {
            $viewers = ProductOrderComment::with('viewers.department')
                            ->find( request()->comment_id )
                            ->viewers;

            $users = collect();
            foreach ($viewers as $viewer) {
                if ($viewer->id != auth()->user()->id) {
                    $users->push((object) [
                        'id' => $viewer->id,
                        'name' => $viewer->first_name . ' ' . $viewer->last_name,
                        'department' => $viewer->department == null ? 'Multiple Department' : $viewer->department->description,
                        'viewer' => true,
                    ]); 
                }
            }               

        } else {
            $productOrder = ProductOrder::find($productOrderId);
            $usersArray = User::getUserPerProduct($productOrder->product_id, auth()->user()->company_id);

            $subTaskDetail = SubTaskDetail::find($subTaskDetailId);
            $viewerIds = json_decode( $subTaskDetail->assignee );

            $users = collect();
            foreach ($usersArray as $userItem) {
                if ($userItem->id != auth()->user()->id) {
                    $users->push((object) [
                        'id' => $userItem->id,
                        'name' => $userItem->name,
                        'department' => $userItem->department,
                        'viewer' => in_array($userItem->id, $viewerIds)
                    ]);
                }
            }
        }

        return response([
            'users' => $users
        ], 200);
    }

    public function updateViewers(Request $request, $productOrderId, 
        $subTaskDetailId, $productOrderCommentId)
    {
        if ($request->ajax()) {
            $viewers = $request->viewers;
            $viewerIds = [];
            foreach ($viewers as $viewer) {
                $viewerIds[] = (int) $viewer;
            }
            
            $viewerIds[] = auth()->user()->id;

            if ($request->doForAllComments == 'true') {
                $productOrderComments = 
                    ProductOrderComment::where('sub_task_detail_id', $subTaskDetailId)
                        ->where('user_id', auth()->user()->id)
                        ->get();
                
                foreach ($productOrderComments as $productOrderComment) {
                    $productOrderComment->viewers()->sync( $viewerIds );

                    $condition1 = isset($request->doForAllReplies) && $request->doForAllReplies;
                    $condition2 = $productOrderComment->id == $productOrderCommentId;
                    if ($condition1 && $condition2) {
                        $productOrderComment->load(['children' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        }]);

                        foreach ( $productOrderComment->children as $child ) {
                            $child->viewers()->sync( $viewerIds );
                        }
                    }
                }

            } else {
                $productOrderComment = ProductOrderComment::find($productOrderCommentId);
                $productOrderComment->viewers()->sync( $viewerIds );

                if ( isset($request->doForAllReplies) && $request->doForAllReplies ) {
                    $productOrderComment->load(['children' => function($query) {
                        $query->where('user_id', auth()->user()->id);
                    }]);

                    foreach ( $productOrderComment->children as $child ) {
                        $child->viewers()->sync( $viewerIds );
                    }   
                }

                $productOrderComment->load(['children.viewers' => function($query) {
                    $query->where('user_id', '<>', auth()->user()->id);
                }]);

                foreach ( $productOrderComment->children as $child ) {
                    $currentViewerIds = [];
                    foreach ($child->viewers as $viewer) {
                        $currentViewerIds[] = $viewer->id;
                    }

                    dd(array_merge( 
                        array_intersect($viewerIds, $currentViewerIds), 
                        array_diff($currentViewerIds, $viewerIds) 
                    ));
                    $child->viewers()->sync( array_merge( 
                        array_intersect($viewerIds, $currentViewerIds), 
                        array_diff($currentViewerIds, $viewerIds) 
                    ));
                }
            }

            return response(null, 200);
        }
    }

    /** No route functions */

    private function createEmailOnQueue($productOrderComment)
    {
        $subject  = "Product Order #{$productOrderComment->product_order_id} | "; 
        $subject .= "Task - {$productOrderComment->subTaskDetail->name}"; 

        $user = $productOrderComment->user;
        $fullName = "{$user->first_name} {$user->last_name}";
        $partnerId = $productOrderComment->partner_id;
        $orderId = $productOrderComment->product_order_id;

        $body  = "Product Order #{$productOrderComment->product_order_id} <br />"; 
        $body .= "Task - {$productOrderComment->subTaskDetail->name} <br /> <br />"; 
        $body .= "<a href='" . url("/merchants/workflow/{$partnerId}/{$orderId}") . "'>";

        if (is_null($productOrderComment->parent_id)) {
            $body .= "{$fullName} commented on a Task <br /><br />";
        } else {
            $body .= "{$fullName} replied on a comment <br /><br />";
        }

        $body .= "</a>";
        $body .= "<span style='font-size:0.65em'>(message content)</span> <br />";
        $body .= "{$productOrderComment->comment}";

        $emailAddresses = [];
        foreach ($productOrderComment->viewers as $viewer) {
            if ($viewer->id != auth()->user()->id) {
                $emailAddresses[]  = $viewer->email_address;
            }
        }

        $data = [
            'email' => (object) [
                'subject' => $subject, 
                'body'=> $body
            ]
        ];

        EmailOnQueue::create([
            'subject' => $subject,
            'body' => view("mails.basic4", $data)->render(),
            'email_address' => implode(',', $emailAddresses),
            'create_by' => auth()->user()->username,
            'is_sent' => 0,
        ]);
    }
    
}
