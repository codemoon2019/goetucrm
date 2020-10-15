<?php

namespace App\Http\Requests\CompanySettings;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketReasonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {        
        return [
            'description' => 'required|max:20',
            'department' => 'required|exists:user_types,id',
            'ticket_priority' => 'required|exists:ticket_priorities,code',
        ];
    }
}
