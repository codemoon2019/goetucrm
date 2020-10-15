<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Owner extends Model
{
    /**
     * Required: yes
     *
     * @var Address
     */
    protected $ownerAddress;

    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $ownerEmail;

    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $ownerName;

    /**
     * MM/DD/YYYY
     * Required: no
     * Size: 8
     *
     * @var string
     */
    protected $ownerDob;

    /**
     * ###-###-####
     * Required: yes
     * Size: 12
     *
     * @var string
     */
    protected $ownerPhone;

    /**
     * ###-###-####
     * Required: yes
     * Size: 12
     *
     * @var string
     */
    protected $ownerMobilePhone;

    /**
     * ###-##-####
     * Required: yes
     * Size: 11
     *
     * @var string
     */
    protected $ownerSSN;

    /**
     * https://developer.cardconnect.com/copilotapi#owner-definition
     *
     * Required: yes
     * Size: 200
     *
     * @var string
     */
    protected $ownerTitle;

    /**
     * @return Address
     */
    public function getOwnerAddress(): Address
    {
        return $this->ownerAddress;
    }

    /**
     * @param Address $ownerAddress
     * @return Owner
     */
    public function setOwnerAddress(Address $ownerAddress): Owner
    {
        $this->ownerAddress = $ownerAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    /**
     * @param string $ownerEmail
     * @return Owner
     */
    public function setOwnerEmail(string $ownerEmail): Owner
    {
        $this->ownerEmail = $ownerEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @param string $ownerName
     * @return Owner
     */
    public function setOwnerName(string $ownerName): Owner
    {
        $this->ownerName = $ownerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerDob(): string
    {
        return $this->ownerDob;
    }

    /**
     * @param string $ownerDob
     * @return Owner
     */
    public function setOwnerDob(string $ownerDob): Owner
    {
        $this->ownerDob = $ownerDob;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerPhone(): string
    {
        return $this->ownerPhone;
    }

    /**
     * @param string $ownerPhone
     * @return Owner
     */
    public function setOwnerPhone(string $ownerPhone): Owner
    {
        $this->ownerPhone = $ownerPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerMobilePhone(): string
    {
        return $this->ownerMobilePhone;
    }

    /**
     * @param string $ownerMobilePhone
     * @return Owner
     */
    public function setOwnerMobilePhone(string $ownerMobilePhone): Owner
    {
        $this->ownerMobilePhone = $ownerMobilePhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerSSN(): string
    {
        return $this->ownerSSN;
    }

    /**
     * @param string $ownerSSN
     * @return Owner
     */
    public function setOwnerSSN(string $ownerSSN): Owner
    {
        $this->ownerSSN = $ownerSSN;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerTitle(): string
    {
        return $this->ownerTitle;
    }

    /**
     * @param string $ownerTitle
     * @return Owner
     */
    public function setOwnerTitle(string $ownerTitle): Owner
    {
        $this->ownerTitle = $ownerTitle;
        return $this;
    }
}
