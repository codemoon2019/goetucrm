<?php

namespace Guesl\CardConnect\CoPilot\Models;

class BusinessDetails extends Model
{
    /**
     * Required: no
     *
     * @var boolean
     */
    protected $customerBillPriorToShipFlg;

    /**
     * Required: no
     *
     * @var boolean
     */
    protected $depositReqForFulfillFlg;

    /**
     * https://developer.cardconnect.com/copilotapi#when-customer-charged-enumeration-definition
     *
     * "In Advance" or "When Service Provided".
     *
     * Required: no
     * Size: 10
     *
     * @var string
     */
    protected $whenCustomerChargedCd;

    /**
     * https://developer.cardconnect.com/copilotapi#refund-policy-enumeration-definition
     *
     * "Exchange Only", "No Refund or Exchange", "More than 30 days" or "30 Days or Less".
     *
     * Required: no
     * Size: 10
     *
     * @var string
     */
    protected $refundPolicyCd;

    /**
     * https://developer.cardconnect.com/copilotapi#service-provided-in-enumeration-definition
     *
     * "30 Days or Less", "31 to 60 Days" or "60+ Days".
     *
     * Required: no
     * Size: 10
     *
     * @var string
     */
    protected $serviceProvidedInCd;

    /**
     * @return bool
     */
    public function isCustomerBillPriorToShipFlg(): bool
    {
        return $this->customerBillPriorToShipFlg;
    }

    /**
     * @param bool $customerBillPriorToShipFlg
     * @return BusinessDetails
     */
    public function setCustomerBillPriorToShipFlg(bool $customerBillPriorToShipFlg): BusinessDetails
    {
        $this->customerBillPriorToShipFlg = $customerBillPriorToShipFlg;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDepositReqForFulfillFlg(): bool
    {
        return $this->depositReqForFulfillFlg;
    }

    /**
     * @param bool $depositReqForFulfillFlg
     * @return BusinessDetails
     */
    public function setDepositReqForFulfillFlg(bool $depositReqForFulfillFlg): BusinessDetails
    {
        $this->depositReqForFulfillFlg = $depositReqForFulfillFlg;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhenCustomerChargedCd(): string
    {
        return $this->whenCustomerChargedCd;
    }

    /**
     * @param string $whenCustomerChargedCd
     * @return BusinessDetails
     */
    public function setWhenCustomerChargedCd(string $whenCustomerChargedCd): BusinessDetails
    {
        $this->whenCustomerChargedCd = $whenCustomerChargedCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefundPolicyCd(): string
    {
        return $this->refundPolicyCd;
    }

    /**
     * @param string $refundPolicyCd
     * @return BusinessDetails
     */
    public function setRefundPolicyCd(string $refundPolicyCd): BusinessDetails
    {
        $this->refundPolicyCd = $refundPolicyCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceProvidedInCd(): string
    {
        return $this->serviceProvidedInCd;
    }

    /**
     * @param string $serviceProvidedInCd
     * @return BusinessDetails
     */
    public function setServiceProvidedInCd(string $serviceProvidedInCd): BusinessDetails
    {
        $this->serviceProvidedInCd = $serviceProvidedInCd;
        return $this;
    }
}
