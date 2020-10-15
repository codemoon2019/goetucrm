<?php

namespace App\Http\Requests\SupplierLeads;

use Illuminate\Foundation\Http\FormRequest;

class EditSupplierLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'doing_business_as' => 'required|string',
            'business_name' => 'nullable|string',
            'mcc' => 'required|string|exists:business_types,mcc',
            
            'business_address' => 'required|string',
            'business_address_2' => 'nullable|string',
            'country' => 'required|integer|exists:countries,id',
            'state' => 'required|integer|exists:states,id',
            'city' => 'required|string',
            'zip' => 'required|integer',

            'business_phone' => 'required|string',
            'extension' => 'nullable|integer',
            'fax' => 'nullable|string',

            'business_phone_2' => 'nullable|string',
            'extension_2' => 'nullable|integer',
            
            'business_email' => 'nullable|string|email',
        ];
    }
}
