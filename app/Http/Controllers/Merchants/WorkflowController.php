<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\CreateSubtaskRequest;
use App\Models\Partner;
use App\Models\ProductOrder;
use App\Models\SubTaskHeader as Task;
use App\Models\SubTaskDetail as Subtask;
use App\Models\TicketActivity;
use App\Models\TicketStatus;
use App\Services\Workflow\TicketGenerator;
use App\Services\Workflow\TaskService;
use App\Services\Products\Workflow\WorkflowDependencies;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function showWorkflow(
        int $merchantId, 
        int $productOrderId,
        TaskService $taskService)
    {
        $partner = Partner::findOrFail($merchantId);
        $productOrder = ProductOrder::findOrFail($productOrderId);
        $product = $productOrder->product;
        $task = $taskService->getTask($productOrder);
        $subtasks = $task->subtasks->groupBy('ticketHeader.status');

        $workflowDependencies = new WorkflowDependencies($product);

        $ticketHeaderIds = $task->subtasks
            ->pluck('ticketHeader.id')
            ->filter()
            ->all();

        $recentActivities = TicketActivity::select([
                'id', 
                'main_action', 
                'ticket_header_id',
                'create_by',
                'update_by'
            ]) 
            ->with('ticketHeader')
            ->with('createdBy')
            ->whereIn('ticket_header_id', $ticketHeaderIds)
            ->orderBy('id', 'DESC')
            ->get();

        return view('merchants.workflow')->with([
            'merchant' => $partner,
            'product' => $product,
            'productOrder' => $productOrder,
            'recentActivities' => $recentActivities,
            'subtasks' => $subtasks,
            'task' => $task,
            'workflowDependencies' => $workflowDependencies,
        ]);
    }

    public function changeSubtaskStatus(int $subtaskId, Request $request)
    {
        $subtask = Subtask::findOrFail($subtaskId);
        $subtask->ticketHeader->status = $request->status_code;
        $subtask->ticketHeader->save();
        
        if ($request->status_code == TicketStatus::SOLVED) {
            $ticketGenerator = new TicketGenerator($subtask->task);
            $generatedTickets = $ticketGenerator->generateTickets();

            $subtask->status = 'C';
            $subtask->save();
            
            return response()->json([
                'generated_tickets' => $generatedTickets
            ], 200);
        }

        return response()->json(null, 200);
    }

    public function createSubtask(
        int $taskId,
        CreateSubtaskRequest $request)
    {
        $task = Task::findOrFail($taskId);
        $subtaskData = $request->subtask;
        $subtaskData['task_no'] = $task->subtasks()->count() + 1;

        $subtask = $task->subtasks()->create($subtaskData);

        $ticketGenerator = new TicketGenerator($task);
        if ($ticketGenerator->checkIfDoable($subtask)) {
            $ticketGenerator->generateTicket($subtask);
        }

        return redirect()->back()->with(
            'success', 
            'Successfully added subtask');
    }
}
