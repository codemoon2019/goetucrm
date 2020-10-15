<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Signer extends Model
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var bool
     */
    protected $personalGuaranteeFlg;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Signer
     */
    public function setFirstName(string $firstName): Signer
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Signer
     */
    public function setLastName(string $lastName): Signer
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Signer
     */
    public function setEmail(string $email): Signer
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return Signer
     */
    public function setIpAddress(string $ipAddress): Signer
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPersonalGuaranteeFlg(): bool
    {
        return $this->personalGuaranteeFlg;
    }

    /**
     * @param bool $personalGuaranteeFlg
     * @return Signer
     */
    public function setPersonalGuaranteeFlg(bool $personalGuaranteeFlg): Signer
    {
        $this->personalGuaranteeFlg = $personalGuaranteeFlg;
        return $this;
    }
}
