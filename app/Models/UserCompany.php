<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    protected $table = 'user_companies';

	public function company_detail()
    {
        return $this->hasOne('App\Models\PartnerCompany', 'partner_id', 'company_id');
	}

}
