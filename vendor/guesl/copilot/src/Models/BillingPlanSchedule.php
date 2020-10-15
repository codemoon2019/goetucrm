<?php

namespace Guesl\CardConnect\CoPilot\Models;

class BillingPlanSchedule extends Model
{
    /**
     * The id of the billing plan schedule entry.
     *
     * Required: yes
     *
     * @var int
     */
    protected $billingPlanScheduleId;


    /**
     * The amount that was actually billed, if this payment was processed.
     *
     * Required: no
     *
     * @var int
     */
    protected $actualAmount;

    /**
     * The date this payment was made, if it was processed.
     * "MM/DD/YYYY"
     *
     * Required: no
     *
     * @var string
     */
    protected $actualPaymentDate;

    /**
     * Scheduled, Cancelled, Paid, or Failed.
     * Required: yes
     *
     * @var string
     */
    protected $paymentStatus;

    /**
     * The transaction id, if it was processed.
     *
     * Required: no
     *
     * @var string
     */
    protected $retref;

    /**
     * The amount scheduled to be billed.
     *
     * Required: yes
     *
     * @var float
     */
    protected $scheduledAmount;

    /**
     * The date the transaction is scheduled for
     * "MM/DD/YYYY"
     *
     * Required: yes
     *
     * @var int
     */
    protected $scheduledPaymentDate;

    /**
     * @return int
     */
    public function getBillingPlanScheduleId(): int
    {
        return $this->billingPlanScheduleId;
    }

    /**
     * @param int $billingPlanScheduleId
     * @return BillingPlanSchedule
     */
    public function setBillingPlanScheduleId(int $billingPlanScheduleId): BillingPlanSchedule
    {
        $this->billingPlanScheduleId = $billingPlanScheduleId;
        return $this;
    }

    /**
     * @return int
     */
    public function getActualAmount(): int
    {
        return $this->actualAmount;
    }

    /**
     * @param int $actualAmount
     * @return BillingPlanSchedule
     */
    public function setActualAmount(int $actualAmount): BillingPlanSchedule
    {
        $this->actualAmount = $actualAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getActualPaymentDate(): string
    {
        return $this->actualPaymentDate;
    }

    /**
     * @param string $actualPaymentDate
     * @return BillingPlanSchedule
     */
    public function setActualPaymentDate(string $actualPaymentDate): BillingPlanSchedule
    {
        $this->actualPaymentDate = $actualPaymentDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * @return BillingPlanSchedule
     */
    public function setPaymentStatus(string $paymentStatus): BillingPlanSchedule
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getRetref(): string
    {
        return $this->retref;
    }

    /**
     * @param string $retref
     * @return BillingPlanSchedule
     */
    public function setRetref(string $retref): BillingPlanSchedule
    {
        $this->retref = $retref;
        return $this;
    }

    /**
     * @return float
     */
    public function getScheduledAmount(): float
    {
        return $this->scheduledAmount;
    }

    /**
     * @param float $scheduledAmount
     * @return BillingPlanSchedule
     */
    public function setScheduledAmount(float $scheduledAmount): BillingPlanSchedule
    {
        $this->scheduledAmount = $scheduledAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getScheduledPaymentDate(): int
    {
        return $this->scheduledPaymentDate;
    }

    /**
     * @param int $scheduledPaymentDate
     * @return BillingPlanSchedule
     */
    public function setScheduledPaymentDate(int $scheduledPaymentDate): BillingPlanSchedule
    {
        $this->scheduledPaymentDate = $scheduledPaymentDate;
        return $this;
    }
}
