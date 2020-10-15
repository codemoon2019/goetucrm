<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Bank extends Model
{
    /**
     * Required: yes
     * Size: 25
     *
     * @var string
     */
    protected $bankAcctNum;

    /**
     * Required: yes
     * Size: 9
     *
     * @var string
     */
    protected $bankRoutingNum;

    /**
     * https://developer.cardconnect.com/copilotapi#bank-account-type-enumeration-definition
     * "Business Checking", "General Ledger", or "Savings".
     *
     * Required: yes
     * Size: 10
     *
     * @var string
     */
    protected $bankAcctTypeCd;

    /**
     * Required: yes
     * Size: 200
     *
     * @var string
     */
    protected $bankName;

    /**
     * @return string
     */
    public function getBankAcctNum(): string
    {
        return $this->bankAcctNum;
    }

    /**
     * @param string $bankAcctNum
     * @return Bank
     */
    public function setBankAcctNum(string $bankAcctNum): Bank
    {
        $this->bankAcctNum = $bankAcctNum;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankRoutingNum(): string
    {
        return $this->bankRoutingNum;
    }

    /**
     * @param string $bankRoutingNum
     * @return Bank
     */
    public function setBankRoutingNum(string $bankRoutingNum): Bank
    {
        $this->bankRoutingNum = $bankRoutingNum;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankAcctTypeCd(): string
    {
        return $this->bankAcctTypeCd;
    }

    /**
     * @param string $bankAcctTypeCd
     * @return Bank
     */
    public function setBankAcctTypeCd(string $bankAcctTypeCd): Bank
    {
        $this->bankAcctTypeCd = $bankAcctTypeCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return Bank
     */
    public function setBankName(string $bankName): Bank
    {
        $this->bankName = $bankName;
        return $this;
    }
}
