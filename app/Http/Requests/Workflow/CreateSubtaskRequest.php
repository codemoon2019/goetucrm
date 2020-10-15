<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubtaskRequest extends FormRequest
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

        $subtask = [
            'name' => $data['subtask_name'],
            'ticket_priority_code' => $data['priority'],
            'department_id' => $data['department'],
            'assignee' => $data['assignee'] !== 'DEPARTMENT' ? $data['assignee'] : null,
            'days_to_complete' => $data['days_to_complete'],
            'prerequisite' => null,
            'link_condition' => null
        ];

        if (isset($data['has_prerequisite'])) {
            $subtask['prerequisite'] = $data['prereq_subtask_number'];
            $subtask['link_condition'] = $data['start_this_subtask_on'];
        }

        $data['subtask'] = $subtask;

        unset($data['subtask_name']);
        unset($data['priority']);
        unset($data['department']);
        unset($data['days_to_complete']);
        unset($data['prereq_subtask_number']);
        unset($data['start_this_subtask_on']);
        unset($data['has_prerequisite']);
        unset($data['assignee']);

        $this->replace($data);
    }
}
