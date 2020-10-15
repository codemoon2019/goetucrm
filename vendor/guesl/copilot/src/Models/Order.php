<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Order extends Model
{
    /**
     * The merchantId the order is for. (cannot set this value, only used for GET)
     * Required: no
     * Size: 19
     *
     * @var int
     */
    protected $orderId;

    /**
     * The merchantId the order is for.
     * Required: yes
     * Size: 19
     *
     * @var int
     */
    protected $merchantId;

    /**
     * The equipmentId the order is for.
     * Required: yes
     * Size: 19
     *
     * @var int
     */
    protected $equipmentId;

    /**
     * Optional notes for the person fulfilling the order.
     * Required: no
     * Size: 1000
     *
     * @var int
     */
    protected $orderNotes;

    /**
     * The number of devices being ordered.
     * Required: no
     * Size: 4
     *
     * @var int
     */
    protected $quantity;

    /**
     * The price per unit.
     * Required: yes (when billToFrequencyCd is ONETIME)
     * Size: 10
     *
     * @var float
     */
    protected $unitPrice;

    /**
     * The price per month.
     * Required: yes (if billToFrequencyCd is MONTHLY)
     * Size: 4
     *
     * @var float
     */
    protected $monthlyPrice;

    /**
     * Who to bill for this order
     * https://developer.cardconnect.com/copilotapi#bill-to-enumeration-definition
     *
     * "Partner Residuals", "Partner Credit Card", "Internal" or "Customer".
     *
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $billToCd;

    /**
     * Billing frequency
     * https://developer.cardconnect.com/copilotapi#billing-frequency-enumeration-definition
     *
     * "One Time" or "Monthly".
     *
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $billingFrequencyCd;

    /**
     * The profileId of the card to charge for this order.
     * Required: yes (if billToCd is PARTNERCC)
     *
     * @var string
     */
    protected $profileId;

    /**
     * The account number to charge for this order.
     * Required: yes (if billToCd is PARTNERCC)
     *
     * @var int
     */
    protected $acctId;

    /**
     * Required: yes
     *
     * @var OrderShippingDetail
     */
    protected $shippingDetails;

    /**
     * https://developer.cardconnect.com/copilotapi#order-status-enumeration-definition
     *
     * "New", "Cancelled", "Exported", "Placed", "Fulfilling" or "Shipped"
     *
     * Required: no
     *
     * @var string
     */
    protected $orderStatusCd;

    /**
     * The date the order was placed (Usually when right after the merchant signs the application)
     * Required: no
     *
     * @var string
     */
    protected $placedDatetime;

    /**
     * The date the order was shipped
     * Required: no
     *
     * @var string
     */
    protected $shippedDatetime;

    /**
     * The date the order was canceled
     * Required: no
     *
     * @var string
     */
    protected $canceledDatetime;

    /**
     * The date the order was fulfilled
     * Required: no
     *
     * @var string
     */
    protected $fulfillingDatetime;

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return Order
     */
    public function setOrderId(int $orderId): Order
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    /**
     * @param int $merchantId
     * @return Order
     */
    public function setMerchantId(int $merchantId): Order
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return int
     */
    public function getEquipmentId(): int
    {
        return $this->equipmentId;
    }

    /**
     * @param int $equipmentId
     * @return Order
     */
    public function setEquipmentId(int $equipmentId): Order
    {
        $this->equipmentId = $equipmentId;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNotes(): int
    {
        return $this->orderNotes;
    }

    /**
     * @param int $orderNotes
     * @return Order
     */
    public function setOrderNotes(int $orderNotes): Order
    {
        $this->orderNotes = $orderNotes;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Order
     */
    public function setQuantity(int $quantity): Order
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     * @return Order
     */
    public function setUnitPrice(float $unitPrice): Order
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getMonthlyPrice(): float
    {
        return $this->monthlyPrice;
    }

    /**
     * @param float $monthlyPrice
     * @return Order
     */
    public function setMonthlyPrice(float $monthlyPrice): Order
    {
        $this->monthlyPrice = $monthlyPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillToCd(): string
    {
        return $this->billToCd;
    }

    /**
     * @param string $billToCd
     * @return Order
     */
    public function setBillToCd(string $billToCd): Order
    {
        $this->billToCd = $billToCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingFrequencyCd(): string
    {
        return $this->billingFrequencyCd;
    }

    /**
     * @param string $billingFrequencyCd
     * @return Order
     */
    public function setBillingFrequencyCd(string $billingFrequencyCd): Order
    {
        $this->billingFrequencyCd = $billingFrequencyCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getProfileId(): string
    {
        return $this->profileId;
    }

    /**
     * @param string $profileId
     * @return Order
     */
    public function setProfileId(string $profileId): Order
    {
        $this->profileId = $profileId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAcctId(): int
    {
        return $this->acctId;
    }

    /**
     * @param int $acctId
     * @return Order
     */
    public function setAcctId(int $acctId): Order
    {
        $this->acctId = $acctId;
        return $this;
    }

    /**
     * @return OrderShippingDetail
     */
    public function getShippingDetails(): OrderShippingDetail
    {
        return $this->shippingDetails;
    }

    /**
     * @param OrderShippingDetail $shippingDetails
     * @return Order
     */
    public function setShippingDetails(OrderShippingDetail $shippingDetails): Order
    {
        $this->shippingDetails = $shippingDetails;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatusCd(): string
    {
        return $this->orderStatusCd;
    }

    /**
     * @param string $orderStatusCd
     * @return Order
     */
    public function setOrderStatusCd(string $orderStatusCd): Order
    {
        $this->orderStatusCd = $orderStatusCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacedDatetime(): string
    {
        return $this->placedDatetime;
    }

    /**
     * @param string $placedDatetime
     * @return Order
     */
    public function setPlacedDatetime(string $placedDatetime): Order
    {
        $this->placedDatetime = $placedDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippedDatetime(): string
    {
        return $this->shippedDatetime;
    }

    /**
     * @param string $shippedDatetime
     * @return Order
     */
    public function setShippedDatetime(string $shippedDatetime): Order
    {
        $this->shippedDatetime = $shippedDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getCanceledDatetime(): string
    {
        return $this->canceledDatetime;
    }

    /**
     * @param string $canceledDatetime
     * @return Order
     */
    public function setCanceledDatetime(string $canceledDatetime): Order
    {
        $this->canceledDatetime = $canceledDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getFulfillingDatetime(): string
    {
        return $this->fulfillingDatetime;
    }

    /**
     * @param string $fulfillingDatetime
     * @return Order
     */
    public function setFulfillingDatetime(string $fulfillingDatetime): Order
    {
        $this->fulfillingDatetime = $fulfillingDatetime;
        return $this;
    }
}
