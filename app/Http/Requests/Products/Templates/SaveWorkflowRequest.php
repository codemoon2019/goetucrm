<?php

namespace App\Http\Requests\Products\Templates;

use Illuminate\Foundation\Http\FormRequest;

class SaveWorkflowRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * @todo 
     * 
     * Skipped backend validation due to time constraints
     */
    public function rules()
    {
        $this->sanitize();

        return [];
    }

    public function sanitize()
    {
        $data = $this->all();

        $subtasks = [];
        $length = count($data['subtask_name']);
        for ($i = 0; $i < $length; $i++) {
            $subtask = [
                'line_number' => $i + 1,
                'name' => $data['subtask_name'][$i],
                'product_tags' => json_encode($data['subproducts'][$i]),
                'ticket_priority_code' => $data['priority'][$i],
                'department_id' => $data['department'][$i],
                'assignee' => ($data['assignee'][$i] ?? 'DEPARTMENT') !== 'DEPARTMENT' ? $data['assignee'][$i] : null,
                'days_to_complete' => $data['days_to_complete'][$i],
                'prerequisite' => null,
                'link_condition' => null
            ];

            if ($i == 0) {
                $subtasks[] = $subtask;
                continue;
            } else if (isset($data['has_prerequisite'][$i])) {
                $subtask['prerequisite'] = $data['prereq_subtask_number'][$i - 1];
                $subtask['link_condition'] = $data['start_this_subtask_on'][$i - 1];
            }
            
            $subtasks[] = $subtask;
        }

        $data['subtasks'] = $subtasks;

        unset($data['subtask_name']);
        unset($data['subproducts']);
        unset($data['priority']);
        unset($data['department']);
        unset($data['days_to_complete']);
        unset($data['prereq_subtask_number']);
        unset($data['start_this_subtask_on']);
        unset($data['has_prerequisite']);

        $this->replace($data);
    }
}
