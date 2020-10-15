<?php

namespace App\Contracts\Workflow;

use App\Models\ProductOrder;
use App\Models\SubTaskDetail;
use App\Models\UserType;
use Illuminate\Http\Request;

interface WorkflowNotifyService
{
    public function notifyOnCreate(ProductOrder $productOrder, $associatedUsers);
    public function notifyOnAssign(ProductOrder $productOrder, SubTaskDetail $subTaskDetail);
    public function notifyOnCompletion(ProductOrder $productOrder, 
        SubTaskDetail $subTaskDetailPreReq, SubTaskDetail $subTaskDetail);
}