<?php

namespace App\Http\Requests\Developers;

use App\Models\Access;
use Illuminate\Foundation\Http\FormRequest;

class ApiKeyStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'project_name' => 'required|max:20',
            'key' => 'required|size:15',
            'note' => 'nullable|max:200',
        ];
        
        if (Access::hasPageAccess('admin', 'super admin access', true)) {
            $rules['user_id'] = 'required';
        }

        return $rules;
    }
}
