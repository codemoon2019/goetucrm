<?php

namespace App\Http\Requests\Reports\UserActivities;

use App\Models\UserType;
use App\Rules\UserTypeHasUsersRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_type' => ['bail', 'required', 'exists:user_types,id', new UserTypeHasUsersRule($this->company_id)],
            'user' => 'nullable|exists:users,id',
            'date_type' => 'required',
            'date' => 'required_if:date_type,day|
                       required_if:date_type,month|
                       required_if:date_type,year',
            'custom_start_date' => 'required_if:date_type,custom',
            'custom_end_date' => 'required_if:date_type,custom|nullable|after:custom_start_date',
            'display_by' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'user_type.exists' => 'Selected group/department does not have any users.'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($validator->getMessageBag()->toArray())) {
                $this->sanitize();
            }
        });
    }

    public function sanitize()
    {
        $data = $this->all();
        $data = $this->sanitizeDate($data);
        $data = $this->sanitizeReportType($data);
        $data = $this->sanitizeResource($data);

        unset($data['date_type']);
        unset($data['date']);
        unset($data['excel']);
        unset($data['user']);
        unset($data['user_type']);
        unset($data['web']);

        $this->replace($data);
    }

    private function sanitizeDate($data)
    {
        switch ($data['date_type']) {
            case 'day':
                $data['start_date'] = Carbon::parse($data['date']);
                $data['end_date'] = (clone $data['start_date']);
                break;

            case 'month':
                $data['start_date'] = Carbon::parse($data['date']);
                $data['end_date'] = (clone $data['start_date'])->endOfMonth();
                break;

            case 'year':
                $data['start_date'] = Carbon::parse($data['date'])->firstOfYear();
                $data['end_date'] = (clone $data['start_date'])->endOfYear();
                break;

            case 'week':
            case 'custom':
                $data['start_date'] = Carbon::parse($data['custom_start_date']);
                $data['end_date'] = Carbon::parse($data['custom_end_date']);
        }
        
        return $data;
    }

    private function sanitizeReportType($data)
    {
        if (array_key_exists('web', $data)) {
            $data['report_type'] = 'web';
        } else if (array_key_exists('excel', $data)) {
            $data['report_type'] = 'excel';
        }

        return $data;
    }

    private function sanitizeResource($data)
    {
        if (!isset($data['user']) && $data['user_type'] <= 13) {
            $data['resource_type'] = 'usertype';
            $data['resource_id'] = $data['user_type'];
        } else if (!isset($data['user']) && $data['user_type'] > 13) {
            $data['resource_type'] = 'department';
            $data['resource_id'] = $data['user_type'];
            $data['company_id'] = null;
        } else {
            $data['resource_type'] = 'user';
            $data['resource_id'] = $data['user'];
            $data['company_id'] = null;
        }

        return $data;
    }
}
