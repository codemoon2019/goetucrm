<?php

namespace App\Http\Requests\SupplierLeads;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateSupplierLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();

        return [
            'doing_business_as' => 'required|string',
            'business_name' => 'nullable|string',
            'mcc' => 'required|string|exists:business_types,mcc',
            
            'business_address' => 'required|string',
            'business_address_2' => 'nullable|string',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required|string',
            'zip' => 'required|integer',

            'business_phone' => 'required|string',
            'extension' => 'nullable|string',
            'fax' => 'nullable|string',

            'business_phone_2' => 'nullable|string',
            'extension_2' => 'nullable|string',
            
            'business_email' => 'nullable|string',

            'contact_first_names.*' => 'required|string',
            'contact_middle_names.*' => 'nullable|string',
            'contact_last_names.*' => 'required|string',
            'contact_positions.*' => 'required|string',
            'contact_mobiles.*' => 'nullable|string',
            'contact_phones.*' => 'nullable|string',
            'contact_phones_2.*' => 'nullable|string',
            'contact_faxs.*' => 'nullable|string',

            'product_names.*' => 'required|string',
            'product_descriptions.*' => 'required|string|max:250',
            'product_prices.*' => 'required|numeric',
        ];
    }

    public function sanitize()
    {
        $data = $this->all();

        if (isset($data['assignee'])) {
            $partnerId = $data['assignee'];
        } else {
            $user = User::find(Auth::id());
            $partnerId = $user->partner->id ?? null;
        }

        $contacts = [];
        $length = count($data['contact_first_names']);
        for ($i = 0; $i < $length; $i++) {
            $contacts[] = [
              'first_name' => $data['contact_first_names'][$i],
              'middle_name'  => $data['contact_middle_names'][$i],
              'last_name' => $data['contact_last_names'][$i],
              'position' => $data['contact_positions'][$i],
              'mobile' => $data['contact_mobiles'][$i],
              'contact_phone' => $data['contact_phones'][$i],
              'contact_phone_2' => $data['contact_phones_2'][$i],
              'fax' => $data['contact_faxs'][$i],
            ];
        }

        $products = [];
        $length = count($data['product_names'] ?? []);
        for ($i = 0; $i < $length; $i++) {
            $products[] = [
                'name' => $data['product_names'][$i],
                'description' => $data['product_descriptions'][$i],
                'price' => $data['product_prices'][$i]
            ];
        }

        $data['contacts'] = $contacts;
        $data['partner_id'] = $partnerId;
        $data['products'] = $products;
        
        unset($data['contact_first_names']);
        unset($data['contact_middle_names']);
        unset($data['contact_last_names']);
        unset($data['contact_position_names']);
        unset($data['contact_emails']);
        unset($data['contact_mobiles']);
        unset($data['contact_phones']);
        unset($data['contact_phones_2']);
        unset($data['contact_faxs']);
        unset($data['product_names']);
        unset($data['product_prices']);
        unset($data['product_description']);

        $this->replace($data);
    }
}
