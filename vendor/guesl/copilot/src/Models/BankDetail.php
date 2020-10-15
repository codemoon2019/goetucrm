<?php

namespace Guesl\CardConnect\CoPilot\Models;

class BankDetail extends Model
{
    /**
     * Required: yes
     *
     * @var Bank
     */
    protected $depositBank;

    /**
     * @var Bank
     */
    protected $withdrawalBank;

    /**
     * Required: yes
     *
     * @return Bank
     */
    public function getDepositBank(): Bank
    {
        return $this->depositBank;
    }

    /**
     * @param Bank $depositBank
     * @return BankDetail
     */
    public function setDepositBank(Bank $depositBank): BankDetail
    {
        $this->depositBank = $depositBank;
        return $this;
    }

    /**
     * @return Bank
     */
    public function getWithdrawalBank(): Bank
    {
        return $this->withdrawalBank;
    }

    /**
     * @param Bank $withdrawalBank
     * @return BankDetail
     */
    public function setWithdrawalBank(Bank $withdrawalBank): BankDetail
    {
        $this->withdrawalBank = $withdrawalBank;
        return $this;
    }
}
