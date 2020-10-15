<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Demographic extends Model
{
    /**
     * Like: "CO", "AL", "AK", "NY"...
     * https://developer.cardconnect.com/copilotapi#state-codes
     *
     * Required: no
     * Size: 2
     *
     * @var string
     */
    protected $businessIncorporatedStateCd;

    /**
     * one of (PT, MT, CT, ET, HST)
     *
     * Required: no
     * Size: 3
     *
     * @var string
     */
    protected $merchantTimeZone;

    /**
     * Required if merchant -> processing -> modeOfTransaction-> eCommercePct is greater than 0
     *
     * Required: yes (if merchant -> processing -> modeOfTransaction-> eCommercePct is greater than 0)
     * Size: 200
     *
     * @var string
     */
    protected $websiteAddress;

    /**
     * ###-###-####
     *
     * Required: no
     * Size: 12
     *
     * @var string
     */
    protected $businessPhone;

    /**
     * ###-###-####
     *
     * Required: no
     * Size: 12
     *
     * @var string
     */
    protected $businessFax;

    /**
     * Required: yes
     *
     * @var Address
     */
    protected $businessAddress;

    /**
     * Required: yes
     *
     * @var Address
     */
    protected $mailingAddress;

    /**
     * @return string
     */
    public function getBusinessIncorporatedStateCd(): string
    {
        return $this->businessIncorporatedStateCd;
    }

    /**
     * @param string $businessIncorporatedStateCd
     * @return Demographic
     */
    public function setBusinessIncorporatedStateCd(string $businessIncorporatedStateCd): Demographic
    {
        $this->businessIncorporatedStateCd = $businessIncorporatedStateCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantTimeZone(): string
    {
        return $this->merchantTimeZone;
    }

    /**
     * @param string $merchantTimeZone
     * @return Demographic
     */
    public function setMerchantTimeZone(string $merchantTimeZone): Demographic
    {
        $this->merchantTimeZone = $merchantTimeZone;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsiteAddress(): string
    {
        return $this->websiteAddress;
    }

    /**
     * @param string $websiteAddress
     * @return Demographic
     */
    public function setWebsiteAddress(string $websiteAddress): Demographic
    {
        $this->websiteAddress = $websiteAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessPhone(): string
    {
        return $this->businessPhone;
    }

    /**
     * @param string $businessPhone
     * @return Demographic
     */
    public function setBusinessPhone(string $businessPhone): Demographic
    {
        $this->businessPhone = $businessPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessFax(): string
    {
        return $this->businessFax;
    }

    /**
     * @param string $businessFax
     * @return Demographic
     */
    public function setBusinessFax(string $businessFax): Demographic
    {
        $this->businessFax = $businessFax;
        return $this;
    }

    /**
     * @return Address
     */
    public function getBusinessAddress(): Address
    {
        return $this->businessAddress;
    }

    /**
     * @param Address $businessAddress
     * @return Demographic
     */
    public function setBusinessAddress(Address $businessAddress): Demographic
    {
        $this->businessAddress = $businessAddress;
        return $this;
    }

    /**
     * @return Address
     */
    public function getMailingAddress(): Address
    {
        return $this->mailingAddress;
    }

    /**
     * @param Address $mailingAddress
     * @return Demographic
     */
    public function setMailingAddress(Address $mailingAddress): Demographic
    {
        $this->mailingAddress = $mailingAddress;
        return $this;
    }
}
