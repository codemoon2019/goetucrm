<?php

namespace Guesl\CardConnect\CoPilot\Models;

class BillingPlan extends Model
{
    /**
     * The id of the billing plan, should be null when creating a new plan
     *
     * Required: no
     * Size: 11
     *
     * @var int
     */
    protected $billingPlanId;


    /**
     * The mid the billing plan is being created for
     *
     * Required: yes
     * Size: 11
     *
     * @var int
     */
    protected $merchId;

    /**
     * The profile being billed
     *
     * Required: yes
     * Size: 20
     *
     * @var int
     */
    protected $profileId;

    /**
     * The account id within the profile to use.
     * If not provided when creating a billing plan, the default account will be used. This field is always returned when retrieving a billing plan.
     *
     * Required: no
     * Size: 3
     *
     * @var string
     */
    protected $acctId;

    /**
     * The amount to bill per time period
     *
     * Required: yes
     *
     * @var float
     */
    protected $amount;

    /**
     * 1 for daily, 2 for weekly, 3 for monthly, or 4 for yearly
     *
     * Required: yes
     *
     * @var int
     */
    protected $timeSpan;

    /**
     * Bill the customer every x time periods
     *
     * Required: yes
     *
     * @var int
     */
    protected $every;

    /**
     * C for cancelled, N for number of payments, D for date
     *
     * Required: yes
     * Size: 1
     *
     * @var string
     */
    protected $untilCondition;

    /**
     * Required if untilCondition = N
     *
     * Required: yes (if untilCondition = N)
     *
     * @var int
     */
    protected $untilNumPayments;

    /**
     * Required if untilCondition = D. Date format: MMDDYYYY
     *
     * Required: yes (if untilCondition = D. Date format: MMDDYYYY)
     * Size: 8
     *
     * @var String
     */
    protected $untilDate;

    /**
     * The currency symbol for the transaction. Like "$".
     *
     * Required: yes
     * Size: 1
     *
     * @var string
     */
    protected $currencySymbol;

    /**
     * Date format: MMDDYYYY.
     *
     * Required: yes
     * Size: 8
     *
     * @var string
     */
    protected $startDate;

    /**
     * The email_receipt option should be provided with a value of 0 for no or 1 for yes. See example JSON.
     *
     * "options": [{
     *   "name": "email_receipt",
     *   "value": "0"
     * }],
     *
     * Required: yes
     *
     * @var array
     */
    protected $options;

    /**
     * The billing plan status. A for active or C for cancelled.
     *
     * Required: no
     * Size: 1
     *
     * @var string
     */
    protected $planSatus;

    /**
     * An array of the payments in the billing plan.
     * This cannot be set, it is only returned when fetching a billing plan. This array will always contain all past payments.
     * In addition, it will also contain future scheduled payments
     * (up to 1000 when untilCondition = N or D, or one year of payments when untilCondition = C)
     *
     * Required: no
     *
     * @var array
     */
    protected $billingPlanSchedules;

    /**
     * @return int
     */
    public function getBillingPlanId(): int
    {
        return $this->billingPlanId;
    }

    /**
     * @param int $billingPlanId
     * @return BillingPlan
     */
    public function setBillingPlanId(int $billingPlanId): BillingPlan
    {
        $this->billingPlanId = $billingPlanId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchId(): int
    {
        return $this->merchId;
    }

    /**
     * @param int $merchId
     * @return BillingPlan
     */
    public function setMerchId(int $merchId): BillingPlan
    {
        $this->merchId = $merchId;
        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId(): int
    {
        return $this->profileId;
    }

    /**
     * @param int $profileId
     * @return BillingPlan
     */
    public function setProfileId(int $profileId): BillingPlan
    {
        $this->profileId = $profileId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAcctId(): string
    {
        return $this->acctId;
    }

    /**
     * @param string $acctId
     * @return BillingPlan
     */
    public function setAcctId(string $acctId): BillingPlan
    {
        $this->acctId = $acctId;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return BillingPlan
     */
    public function setAmount(float $amount): BillingPlan
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeSpan(): int
    {
        return $this->timeSpan;
    }

    /**
     * @param int $timeSpan
     * @return BillingPlan
     */
    public function setTimeSpan(int $timeSpan): BillingPlan
    {
        $this->timeSpan = $timeSpan;
        return $this;
    }

    /**
     * @return int
     */
    public function getEvery(): int
    {
        return $this->every;
    }

    /**
     * @param int $every
     * @return BillingPlan
     */
    public function setEvery(int $every): BillingPlan
    {
        $this->every = $every;
        return $this;
    }

    /**
     * @return string
     */
    public function getUntilCondition(): string
    {
        return $this->untilCondition;
    }

    /**
     * @param string $untilCondition
     * @return BillingPlan
     */
    public function setUntilCondition(string $untilCondition): BillingPlan
    {
        $this->untilCondition = $untilCondition;
        return $this;
    }

    /**
     * @return int
     */
    public function getUntilNumPayments(): int
    {
        return $this->untilNumPayments;
    }

    /**
     * @param int $untilNumPayments
     * @return BillingPlan
     */
    public function setUntilNumPayments(int $untilNumPayments): BillingPlan
    {
        $this->untilNumPayments = $untilNumPayments;
        return $this;
    }

    /**
     * @return String
     */
    public function getUntilDate(): String
    {
        return $this->untilDate;
    }

    /**
     * @param String $untilDate
     * @return BillingPlan
     */
    public function setUntilDate(String $untilDate): BillingPlan
    {
        $this->untilDate = $untilDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    /**
     * @param string $currencySymbol
     * @return BillingPlan
     */
    public function setCurrencySymbol(string $currencySymbol): BillingPlan
    {
        $this->currencySymbol = $currencySymbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     * @return BillingPlan
     */
    public function setStartDate(string $startDate): BillingPlan
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return BillingPlan
     */
    public function setOptions(array $options): BillingPlan
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlanSatus(): string
    {
        return $this->planSatus;
    }

    /**
     * @param string $planSatus
     * @return BillingPlan
     */
    public function setPlanSatus(string $planSatus): BillingPlan
    {
        $this->planSatus = $planSatus;
        return $this;
    }

    /**
     * @return array
     */
    public function getBillingPlanSchedules(): array
    {
        return $this->billingPlanSchedules;
    }

    /**
     * @param array $billingPlanSchedules
     * @return BillingPlan
     */
    public function setBillingPlanSchedules(array $billingPlanSchedules): BillingPlan
    {
        $this->billingPlanSchedules = $billingPlanSchedules;
        return $this;
    }
}
