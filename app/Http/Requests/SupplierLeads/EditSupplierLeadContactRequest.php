<?php

namespace App\Http\Requests\SupplierLeads;

use Illuminate\Foundation\Http\FormRequest;

class EditSupplierLeadContactRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();

        return [
            'contact_first_names.*' => 'required|string',
            'contact_middle_names.*' => 'nullable|string',
            'contact_last_names.*' => 'required|string',
            'contact_positions.*' => 'required|string',
            'contact_contact_phones.*' => 'nullable|string',
            'contact_contact_phones_2.*' => 'nullable|string',
            'contact_faxs.*' => 'nullable|string',
            'contact_mobiles.*' => 'nullable|string',
        ];
    }

    public function sanitize()
    {
        $data = $this->all();

        $contacts = [];
        $length = count($data['contact_first_names']);
        for ($i = 0; $i < $length; $i++) {
            $contacts[] = [
                'id' => $data['contact_ids'][$i] ?? null,
                'first_name' => $data['contact_first_names'][$i],
                'middle_name' => $data['contact_middle_names'][$i],
                'last_name' => $data['contact_last_names'][$i],
                'position' => $data['contact_positions'][$i],
                'contact_phone' => $data['contact_phones'][$i],
                'contact_phone_2' => $data['contact_phones_2'][$i],
                'fax' => $data['contact_faxs'][$i],
                'mobile' => $data['contact_mobiles'][$i],
            ];
        }

        $data['contacts'] = $contacts;
        
        unset($data['contact_first_names']);
        unset($data['contact_middle_names']);
        unset($data['contact_last_names']);
        unset($data['contact_positions']);
        unset($data['contact_phones']);
        unset($data['contact_phones_2']);
        unset($data['contact_faxs']);
        unset($data['contact_mobiles']);

        $this->replace($data);
    }
}
