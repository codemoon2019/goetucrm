<?php

namespace App\Services\Workflow;

use App\Models\ProductOrder;
use App\Models\SubTaskHeader as Task;
use App\Models\SubTaskTemplateHeader as TaskTemplate;
use Illuminate\Support\Facades\Auth;

class TaskTemplateAdapter
{
    private $taskTemplate;

    public function __construct(TaskTemplate $taskTemplate)
    {
        $this->taskTemplate = $taskTemplate;
    }

    public function getTaskData() : array
    {
        return [
            'name' => $this->taskTemplate->name,
            'description' => $this->taskTemplate->description,
            'remarks' => $this->taskTemplate->remarks,
            'days_to_complete' => $this->taskTemplate->days_to_complete,
            'status' => Task::STATUS_ACTIVE,
            'create_by' => Auth::user()->username,
            'update_by' => Auth::user()->username,
        ];
    }

    public function getSubtasksData(ProductOrder $productOrder) : array
    {
        $productIds = $productOrder->details->pluck('product_id')->all();
        $subtaskTemplates = $this->taskTemplate->subtaskTemplates;
        $subtasksData = [];

        foreach ($subtaskTemplates as $subtaskTemplate) {
            $sameProductIds = array_intersect(
                $productIds, 
                $subtaskTemplate->product_tags);

            if (count($sameProductIds) > 0) {
                $subtasksData[] = [
                    'task_no' => $subtaskTemplate->line_number,
                    'name' => $subtaskTemplate->name,
                    'ticket_priority_code' => $subtaskTemplate->ticket_priority_code,
                    'department_id' => $subtaskTemplate->department_id,
                    'assignee' => $subtaskTemplate->assignee,
                    'days_to_complete' => $subtaskTemplate->days_to_complete,
                    'prerequisite' => $subtaskTemplate->prerequisite,
                    'link_condition' => $subtaskTemplate->link_condition,
                    'update_by' => Auth::user()->username,
                ];
            }
        }

        return $subtasksData;
    }
}