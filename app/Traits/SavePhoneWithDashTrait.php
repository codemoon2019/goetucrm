<?php

namespace App\Traits;

trait SavePhoneWithDashTrait
{
    public function setPhone1Attribute($value)
    {
        $this->attributes['phone1'] = isset($value) ? "-{$value}" : null;
    }

    public function setPhone2Attribute($value)
    {
        $this->attributes['phone2'] = isset($value) ? "-{$value}" : null;
    }

    public function setMobileNumberAttribute($value)
    {
        $this->attributes['mobile_number'] = isset($value) ? "-{$value}" : null;
    }

    public function setMobileNumber2Attribute($value)
    {
        $this->attributes['mobile_number_2'] = isset($value) ? "-{$value}" : null;
    }

    public function setOtherNumberAttribute($value)
    {
        $this->attributes['other_number'] = isset($value) ? "-{$value}" : null;
    }

    public function setOtherNumber2Attribute($value)
    {
        $this->attributes['other_number_2'] = isset($value) ? "-{$value}" : null;
    }

    public function setBusinessPhone1Attribute($value)
    {
        $this->attributes['business_phone1'] = isset($value) ? "-{$value}" : null;
    }

    public function setBusinessPhone2Attribute($value)
    {
        $this->attributes['business_phone2'] = isset($value) ? "-{$value}" : null;
    }
}