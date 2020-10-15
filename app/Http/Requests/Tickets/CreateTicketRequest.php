<?php

namespace App\Http\Requests\Tickets;

use App\Models\User;
use App\Services\Tickets\TicketUserClassification;
use App\Sanitizers\Tickets\CreateTicketSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTicketRequest extends FormRequest
{
    protected $userClassification;

    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'assignee_department' => 'sometimes|required',
            
            'requester' => 'sometimes|required',
            'merchant' => 'sometimes|required_if:requester,M',
            'partner' => 'sometimes|required_if:requester,P',

            'issue_type' => 'sometimes|required',
            'reason' => 'sometimes|required',
            'priority' => 'sometimes|required',

            'subject' => 'required',
            'description' => 'required',
            'attachments.*' => 'nullable|file|max:2000',

            'due_date' => 'sometimes|required',
            'due_time' => 'sometimes|required'
        ];
    }

    public function messages()
    {
        return [
            'department.required' => 'The assignee field is required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($validator->getMessageBag()->toArray())) {
                $sanitizer = new CreateTicketSanitizer(
                    $this->all(),
                    $this->userClassification);

                $this->replace($sanitizer->sanitize());
            }
        });
    }
}
