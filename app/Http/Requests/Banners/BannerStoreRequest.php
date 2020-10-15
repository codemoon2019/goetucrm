<?php

namespace App\Http\Requests\Banners;

use Illuminate\Foundation\Http\FormRequest;

class BannerStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|max:50',
            'message' => 'required|max:500',
            'type' => 'required',
            'starts_at' => 'required|date_format:Y-m-d H:i',
            'ends_at' => 'required|date_format:Y-m-d H:i|after:starts_at',
        ];
        
        if ($this->viewer_type == 'S') {
            $rules['companies'] = 'required_without_all:departments,users';
        }

        return $rules;
    }
}
