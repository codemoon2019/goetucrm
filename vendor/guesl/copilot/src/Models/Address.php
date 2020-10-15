<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Address extends Model
{
    /**
     * Required: yes
     * Size: 200
     *
     * @var string
     */
    protected $address1;

    /**
     * Required: no
     * Size: 200
     *
     * @var string
     */
    protected $address2;

    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $city;

    /**
     * https://developer.cardconnect.com/copilotapi#country-enumeration-definition
     * Required: no
     *
     * @var string
     */
    protected $countryCd;

    /**
     * https://developer.cardconnect.com/copilotapi#state-codes
     * Required: no
     *
     * @var string
     */
    protected $stateCd;

    /**
     * Required: yes
     * Size: 15
     *
     * @var string
     */
    protected $zip;

    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     * @return Address
     */
    public function setAddress1(string $address1): Address
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     * @return Address
     */
    public function setAddress2(string $address2): Address
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCd(): string
    {
        return $this->countryCd;
    }

    /**
     * @param string $countryCd
     * @return Address
     */
    public function setCountryCd(string $countryCd): Address
    {
        $this->countryCd = $countryCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getStateCd(): string
    {
        return $this->stateCd;
    }

    /**
     * @param string $stateCd
     * @return Address
     */
    public function setStateCd(string $stateCd): Address
    {
        $this->stateCd = $stateCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return Address
     */
    public function setZip(string $zip): Address
    {
        $this->zip = $zip;
        return $this;
    }
}
