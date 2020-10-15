<?php

namespace App\Services\Workflow;

use App\Models\ProductOrder;
use App\Models\SubTaskHeader as Task;
use App\Models\SubTaskTemplateHeader as TaskTemplate;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    private function createTask(ProductOrder $productOrder) : Task
    {
        $taskTemplate = TaskTemplate::with('subtaskTemplates')
            ->with('product')
            ->where('product_id', $productOrder->product_id)
            ->where('status', TaskTemplate::STATUS_ACTIVE)
            ->first();

        if (isset($taskTemplate)) {
            $task = $this->createTaskFromTemplate($productOrder, $taskTemplate);
        } else {
            $task = $this->createDefaultTask($productOrder);
        }

        $ticketGenerator = new TicketGenerator($task);
        $ticketGenerator->generateTickets();

        return $task;
    }

    private function createDefaultTask(ProductOrder $productOrder) : Task
    {
        return Task::create([
            'order_id' => $productOrder->id,
            'name' => "{$productOrder->product->name} task",
            'description' => "Auto generated task for {$productOrder->product->name}.",
            'remarks' => "Auto generated task for {$productOrder->product->name}.",
            'status' => Task::STATUS_ACTIVE,
            'create_by' => Auth::user()->username,
            'update_by' => Auth::user()->username,
        ]);
    }

    private function createTaskFromTemplate(
        ProductOrder $productOrder,
        TaskTemplate $taskTemplate) : Task
    {
        $taskTemplateAdapter = new TaskTemplateAdapter($taskTemplate);

        $taskData = $taskTemplateAdapter->getTaskData();
        $taskData['order_id'] = $productOrder->id;

        $subtasksData = $taskTemplateAdapter->getSubtasksData($productOrder);

        $task = Task::create($taskData);
        $task->subtasks()->createMany($subtasksData);
        $task->load('subtasks');

        return $task;
    }

    public function getTask(ProductOrder $productOrder)
    {
        $task = Task::with('subtasks')
            ->with('subtasks.department')
            ->with('subtasks.ticketHeader')
            ->where('order_id', $productOrder->id)
            ->first();

        if (!isset($task)) {
            $task = $this->createTask($productOrder);
            $task->load('subtasks');
            $task->load('subtasks.department');
            $task->load('subtasks.ticketHeader');
        }

        return $task;
    }
}