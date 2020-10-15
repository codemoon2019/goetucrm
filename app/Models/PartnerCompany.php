<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\NoDashPhoneTrait;
use App\Traits\SavePhoneWithDashTrait;
use Illuminate\Database\Eloquent\Model;

class PartnerCompany extends Model
{
    use ActiveTrait, NoDashPhoneTrait, SavePhoneWithDashTrait;

    protected $table = 'partner_companies';

    protected $guarded = [];

    /**
     * Company has one partner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne("App\\Models\\Partner","id","partner_id");
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'partner_id', 'company_id');
    }

    public function departments()
    {
        return $this->hasMany(UserType::class, 'company_id');
    }
}
