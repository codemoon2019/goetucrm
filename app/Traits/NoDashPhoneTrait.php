<?php

namespace App\Traits;

trait NoDashPhoneTrait
{
    public function getNdPhone1Attribute()
    {
        return substr($this->phone1, 1, strlen($this->phone1));
    }

    public function getNdPhone2Attribute()
    {
        return substr($this->phone2, 1, strlen($this->phone2));
    }

    public function getNdMobileNumberAttribute()
    {
        return substr($this->mobile_number, 1, strlen($this->mobile_number));
    }

    public function getNdMobileNumber2Attribute()
    {
        return substr($this->mobile_number_2, 1, strlen($this->mobile_number_2));
    }

    public function getNdOtherNumberAttribute()
    {
        return substr($this->other_number, 1, strlen($this->other_number));
    }

    public function getNdOtherNumber2Attribute()
    {
        return substr($this->other_number_2, 1, strlen($this->other_number_2));
    }

    public function getNdBusinessPhone1Attribute()
    {
        return substr($this->business_phone1, 1, strlen($this->business_phone1));
	}
	
	public function getNdBusinessPhone2Attribute()
    {
        return substr($this->business_phone2, 1, strlen($this->business_phone2));
    }

}