<?php

namespace App\Http\Requests\Tickets;

use App\Models\User;
use App\Services\Tickets\TicketAccessClassification;
use App\Services\Tickets\TicketUserClassification;
use App\Sanitizers\Tickets\EditTicketSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $data = $this->all();

        return [
            'department' => 'sometimes|required',
            'assignee' => 'sometimes|required',
            'reference' => 'sometimes|required',
            'merchant' => 'sometimes|required_if:reference,Merchant',
            'partner' => 'sometimes|required_if:reference,Partner',

            'ticket_type_code' => 'sometimes|required',
            'ticket_reason_code' => 'sometimes|required',
            'ticket_priority_code' => 'sometimes|required',

            'is_internal_note' => 'required',
            'message' => 'required',
            'attachments.*' => 'nullable|file|max:2000',

            'due_date' => 'sometimes|required',
            'due_time' => 'sometimes|required'
        ];
    }

    public function messages()
    {
        return [
            'department.required' => 'The assignee field is required',
            'assignee.required' => 'The assignee field is required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($validator->getMessageBag()->toArray())) {
                $sanitizer = new EditTicketSanitizer(
                    $this->all(),
                    $this->route('status'));

                $this->replace($sanitizer->sanitize());
            }
        });
    }
}
