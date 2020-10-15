<?php

namespace App\Rules;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Contracts\Validation\Rule;

class UserTypeHasUsersRule implements Rule
{
    protected $companyId;

    public function __construct($companyId=null)
    {
        $this->companyId = $companyId;
    }

    public function passes($attribute, $value)
    {
        $userType = UserType::find($value);
        if ($userType->create_by == 'SYSTEM') {
            $usersCount = User::isActive()
                ->whereCompany($this->companyId)
                ->whereUserType($userType->id)
                ->count();
        } else {
            $usersCount = $userType->users()->isActive()->count();
        }

        return $usersCount > 0;
    }

    public function message()
    {
        return "Selected group doesn't have active users";
    }
}
