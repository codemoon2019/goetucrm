<?php

namespace Guesl\CardConnect\CoPilot\Models;

class OrderShippingDetail extends Model
{
    /**
     * Required: yes
     *
     * @var Address
     */
    protected $shippingAddress;

    /**
     * Name of the person receiving the order.
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $shipToAttn;

    /**
     * Email of the person receiving the order.
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $shipToAttnEmail;

    /**
     * The shipping method to be used.
     * https://developer.cardconnect.com/copilotapi#shipping-method-enumeration-definition
     *
     * "STANDARD" or "EXPEDITED".
     *
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $shippingMethodCd;

    /**
     * Who to bill for the shipping (only set if shippingMethodCd is EXPEDITED)
     * https://developer.cardconnect.com/copilotapi#bill-to-enumeration-definition
     *
     * Required: yes (if shippingMethodCd is EXPEDITED)
     * "Partner Residuals"", "Partner Credit Car", "Internal" or "Customer".
     *
     * Size: 100
     *
     * @var string
     */
    protected $shippingBillToCd;

    /**
     * The shipping carrier
     * https://developer.cardconnect.com/copilotapi#shipping-carrier-enumeration-definition
     *
     * "FedEx" or "UPS.
     *
     * Required: no
     * Size: 100
     *
     * @var string
     */
    protected $shippingCarrierCd;

    /**
     * The shipping carrier tracking number.
     * Required: no
     * Size: 100
     *
     * @var string
     */
    protected $trackingNumber;

    /**
     * The shipping cost(Decimal formation).
     * Required: no
     * Size: 100
     *
     * @var float
     */
    protected $shippingCost;

    /**
     * ###-###-####
     * Required: yes
     * Size: 12
     *
     * @var string
     */
    protected $merchantContactPhone;

    /**
     * #####
     * Required: no
     * Size: 5
     *
     * @var string
     */
    protected $merchantContactPhoneExt;

    /**
     * Purchase order number
     * Required: no
     * Size: 30
     *
     * @var string
     */
    protected $poNumber;

    /**
     * @return Address
     */
    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     * @return OrderShippingDetail
     */
    public function setShippingAddress(Address $shippingAddress): OrderShippingDetail
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAttn(): string
    {
        return $this->shipToAttn;
    }

    /**
     * @param string $shipToAttn
     * @return OrderShippingDetail
     */
    public function setShipToAttn(string $shipToAttn): OrderShippingDetail
    {
        $this->shipToAttn = $shipToAttn;
        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAttnEmail(): string
    {
        return $this->shipToAttnEmail;
    }

    /**
     * @param string $shipToAttnEmail
     * @return OrderShippingDetail
     */
    public function setShipToAttnEmail(string $shipToAttnEmail): OrderShippingDetail
    {
        $this->shipToAttnEmail = $shipToAttnEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethodCd(): string
    {
        return $this->shippingMethodCd;
    }

    /**
     * @param string $shippingMethodCd
     * @return OrderShippingDetail
     */
    public function setShippingMethodCd(string $shippingMethodCd): OrderShippingDetail
    {
        $this->shippingMethodCd = $shippingMethodCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingBillToCd(): string
    {
        return $this->shippingBillToCd;
    }

    /**
     * @param string $shippingBillToCd
     * @return OrderShippingDetail
     */
    public function setShippingBillToCd(string $shippingBillToCd): OrderShippingDetail
    {
        $this->shippingBillToCd = $shippingBillToCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCarrierCd(): string
    {
        return $this->shippingCarrierCd;
    }

    /**
     * @param string $shippingCarrierCd
     * @return OrderShippingDetail
     */
    public function setShippingCarrierCd(string $shippingCarrierCd): OrderShippingDetail
    {
        $this->shippingCarrierCd = $shippingCarrierCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     * @return OrderShippingDetail
     */
    public function setTrackingNumber(string $trackingNumber): OrderShippingDetail
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }

    /**
     * @return float
     */
    public function getShippingCost(): float
    {
        return $this->shippingCost;
    }

    /**
     * @param float $shippingCost
     * @return OrderShippingDetail
     */
    public function setShippingCost(float $shippingCost): OrderShippingDetail
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantContactPhone(): string
    {
        return $this->merchantContactPhone;
    }

    /**
     * @param string $merchantContactPhone
     * @return OrderShippingDetail
     */
    public function setMerchantContactPhone(string $merchantContactPhone): OrderShippingDetail
    {
        $this->merchantContactPhone = $merchantContactPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantContactPhoneExt(): string
    {
        return $this->merchantContactPhoneExt;
    }

    /**
     * @param string $merchantContactPhoneExt
     * @return OrderShippingDetail
     */
    public function setMerchantContactPhoneExt(string $merchantContactPhoneExt): OrderShippingDetail
    {
        $this->merchantContactPhoneExt = $merchantContactPhoneExt;
        return $this;
    }

    /**
     * @return string
     */
    public function getPoNumber(): string
    {
        return $this->poNumber;
    }

    /**
     * @param string $poNumber
     * @return OrderShippingDetail
     */
    public function setPoNumber(string $poNumber): OrderShippingDetail
    {
        $this->poNumber = $poNumber;
        return $this;
    }
}
