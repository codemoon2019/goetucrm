<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
     protected $table = 'divisions';

    public function company()
    {
        return $this->hasOne("App\\Models\\PartnerCompany", "partner_id", "company_id");
    }
    public function user()
    {
        return $this->hasOne("App\\Models\\User", "id", "user_id");
    }

}
