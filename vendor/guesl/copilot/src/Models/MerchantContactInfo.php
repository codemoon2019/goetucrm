<?php

namespace Guesl\CardConnect\CoPilot\Models;

class MerchantContactInfo extends Model
{
    /**
     * Required: no
     * Size: 300
     *
     * @var string
     */
    protected $contactName;

    /**
     * Required: no
     * Size: 300
     *
     * @var string
     */
    protected $contactEmail;

    /**
     * ###-###-####
     * Required: no
     * Size: 12
     *
     * @var string
     */
    protected $contactPhone;

    /**
     * @return string
     */
    public function getContactName(): string
    {
        return $this->contactName;
    }

    /**
     * @param string $contactName
     * @return MerchantContactInfo
     */
    public function setContactName(string $contactName): MerchantContactInfo
    {
        $this->contactName = $contactName;
        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     * @return MerchantContactInfo
     */
    public function setContactEmail(string $contactEmail): MerchantContactInfo
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    /**
     * @param string $contactPhone
     * @return MerchantContactInfo
     */
    public function setContactPhone(string $contactPhone): MerchantContactInfo
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }
}
