<?php

use App\Models\User;
use App\Models\UserTypeReference;
use App\Models\UserCompany;
use Illuminate\Database\Seeder;

class UserMultipleCompanySeeder extends Seeder
{
    public function run()
    {
        // UserTypeReference::truncate();
        // UserCompany::truncate();

        $users = User::get();

        foreach ($users as $user) {

            $user_company = New UserCompany;
            $user_company->user_id = $user->id;
            $user_company->company_id = $user->company_id;
            $user_company->save();

            $user_types = explode(',',$user->user_type_id);
            foreach($user_types as $ut){
                $user_type = New UserTypeReference;
                $user_type->user_id = $user->id;
                $user_type->user_type_id = $ut;
                $user_type->save();
            }
            
            $user->save();
            
        }
    }
}
