<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Ownership extends Model
{
    /**
     * Required: yes
     *
     * @var Owner
     */
    protected $owner;

    /**
     * https://developer.cardconnect.com/copilotapi#ownership-type-enumeration-definition
     *
     * Required: no
     *
     * @var string
     */
    protected $ownershipTypeCd;

    /**
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $driversLicenseNumber;

    /**
     * https://developer.cardconnect.com/copilotapi#state-enumeration-definition
     *
     * Required: yes
     * Size: 2
     *
     * @var string
     */
    protected $driversLicenseStateCd;

    /**
     * 0-100%
     *
     * Required: no
     * Size: 3
     *
     * @var int
     */
    protected $ownerOwnershipPct;

    /**
     * @return Owner
     */
    public function getOwner(): Owner
    {
        return $this->owner;
    }

    /**
     * @param Owner $owner
     * @return Ownership
     */
    public function setOwner(Owner $owner): Ownership
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnershipTypeCd(): string
    {
        return $this->ownershipTypeCd;
    }

    /**
     * @param string $ownershipTypeCd
     * @return Ownership
     */
    public function setOwnershipTypeCd(string $ownershipTypeCd): Ownership
    {
        $this->ownershipTypeCd = $ownershipTypeCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getDriversLicenseNumber(): string
    {
        return $this->driversLicenseNumber;
    }

    /**
     * @param string $driversLicenseNumber
     * @return Ownership
     */
    public function setDriversLicenseNumber(string $driversLicenseNumber): Ownership
    {
        $this->driversLicenseNumber = $driversLicenseNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getDriversLicenseStateCd(): string
    {
        return $this->driversLicenseStateCd;
    }

    /**
     * @param string $driversLicenseStateCd
     * @return Ownership
     */
    public function setDriversLicenseStateCd(string $driversLicenseStateCd): Ownership
    {
        $this->driversLicenseStateCd = $driversLicenseStateCd;
        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerOwnershipPct(): int
    {
        return $this->ownerOwnershipPct;
    }

    /**
     * @param int $ownerOwnershipPct
     * @return Ownership
     */
    public function setOwnerOwnershipPct(int $ownerOwnershipPct): Ownership
    {
        $this->ownerOwnershipPct = $ownerOwnershipPct;
        return $this;
    }
}
