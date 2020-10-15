<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Merchant extends Model
{
    /**
     * "akaBusinessName": "Mikes API Merchant AKA1",
     *
     * Required: no
     * Size: 200
     *
     * @var string
     */
    protected $akaBusinessName;

    /**
     * Required: no
     *
     * @var bool
     */
    protected $custPrimaryAcctFlg;

    /**
     * Required: yes
     * Size: 100
     *
     * @var string
     */
    protected $dbaName;

    /**
     * Required: yes
     * Size: 500
     *
     * @var string
     */
    protected $legalBusinessName;

    /**
     * TIN or SSN, can only be SSN if merchant -> ownership -> ownershipType is INDIVSOLE
     *
     * Required: yes
     * Size: 3
     *
     * @var string
     */
    protected $taxFilingMethod;

    /**
     * Required: yes
     * Size: 500
     *
     * @var string
     */
    protected $taxFilingName;

    /**
     * Required: yes
     *
     * @var Demographic
     */
    protected $demographic;

    /**
     * Use the following codes ARTICLE,GOVT
     *
     * Required: no
     *
     * @var string
     */
    protected $businessIdTypeCd;

    /**
     * MM/DD/YYYY
     *
     * Required: no
     * Size: 8
     *
     * @var string
     */
    protected $businessStartDate;

    /**
     * Required: yes
     *
     * @var BankDetail
     */
    protected $bankDetail;

    /**
     * Required: yes
     *
     * @var OwnerShip
     */
    protected $ownership;

    /**
     * Required: no
     *
     * @var MerchantContactInfo
     */
    protected $merchantContactInfo;

    /**
     * Required: yes
     * Size: 25
     *
     * @var null|string
     */
    protected $salesCode;

    /**
     * Required: no
     *
     * @var Processing
     */
    protected $processing;

    /**
     * Required: no
     *
     * @var bool
     */
    protected $webLeadFlg;

    /**
     * Required: no
     *
     * @var Pricing
     */
    protected $pricing;

    /**
     * Required: no
     *
     * @var Fee
     */
    protected $fee;

    /**
     * List of customField
     *
     * Required if the merchant's partner has one or more active required custom fields
     *
     * Required: yes (if the merchant's partner has one or more active required custom fields)
     * Size: 10
     *
     * @see CustomField
     * @var array
     */
    protected $customFields;

    /**
     * @return string
     */
    public function getAkaBusinessName(): string
    {
        return $this->akaBusinessName;
    }

    /**
     * @param string $akaBusinessName
     * @return Merchant
     */
    public function setAkaBusinessName(string $akaBusinessName): Merchant
    {
        $this->akaBusinessName = $akaBusinessName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCustPrimaryAcctFlg(): bool
    {
        return $this->custPrimaryAcctFlg;
    }

    /**
     * @param bool $custPrimaryAcctFlg
     * @return Merchant
     */
    public function setCustPrimaryAcctFlg(bool $custPrimaryAcctFlg): Merchant
    {
        $this->custPrimaryAcctFlg = $custPrimaryAcctFlg;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbaName(): string
    {
        return $this->dbaName;
    }

    /**
     * @param string $dbaName
     * @return Merchant
     */
    public function setDbaName(string $dbaName): Merchant
    {
        $this->dbaName = $dbaName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLegalBusinessName(): string
    {
        return $this->legalBusinessName;
    }

    /**
     * @param string $legalBusinessName
     * @return Merchant
     */
    public function setLegalBusinessName(string $legalBusinessName): Merchant
    {
        $this->legalBusinessName = $legalBusinessName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxFilingMethod(): string
    {
        return $this->taxFilingMethod;
    }

    /**
     * @param string $taxFilingMethod
     * @return Merchant
     */
    public function setTaxFilingMethod(string $taxFilingMethod): Merchant
    {
        $this->taxFilingMethod = $taxFilingMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxFilingName(): string
    {
        return $this->taxFilingName;
    }

    /**
     * @param string $taxFilingName
     * @return Merchant
     */
    public function setTaxFilingName(string $taxFilingName): Merchant
    {
        $this->taxFilingName = $taxFilingName;
        return $this;
    }

    /**
     * @return Demographic
     */
    public function getDemographic(): Demographic
    {
        return $this->demographic;
    }

    /**
     * @param Demographic $demographic
     * @return Merchant
     */
    public function setDemographic(Demographic $demographic): Merchant
    {
        $this->demographic = $demographic;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessIdTypeCd(): string
    {
        return $this->businessIdTypeCd;
    }

    /**
     * @param string $businessIdTypeCd
     * @return Merchant
     */
    public function setBusinessIdTypeCd(string $businessIdTypeCd): Merchant
    {
        $this->businessIdTypeCd = $businessIdTypeCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessStartDate(): string
    {
        return $this->businessStartDate;
    }

    /**
     * @param string $businessStartDate
     * @return Merchant
     */
    public function setBusinessStartDate(string $businessStartDate): Merchant
    {
        $this->businessStartDate = $businessStartDate;
        return $this;
    }

    /**
     * @return BankDetail
     */
    public function getBankDetail(): BankDetail
    {
        return $this->bankDetail;
    }

    /**
     * @param BankDetail $bankDetail
     * @return Merchant
     */
    public function setBankDetail(BankDetail $bankDetail): Merchant
    {
        $this->bankDetail = $bankDetail;
        return $this;
    }

    /**
     * @return OwnerShip
     */
    public function getOwnership(): OwnerShip
    {
        return $this->ownership;
    }

    /**
     * @param OwnerShip $ownership
     * @return Merchant
     */
    public function setOwnership(OwnerShip $ownership): Merchant
    {
        $this->ownership = $ownership;
        return $this;
    }

    /**
     * @return MerchantContactInfo
     */
    public function getMerchantContactInfo(): MerchantContactInfo
    {
        return $this->merchantContactInfo;
    }

    /**
     * @param MerchantContactInfo $merchantContactInfo
     * @return Merchant
     */
    public function setMerchantContactInfo(MerchantContactInfo $merchantContactInfo): Merchant
    {
        $this->merchantContactInfo = $merchantContactInfo;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalesCode(): string
    {
        return $this->salesCode ?: "";
    }

    /**
     * @param string $salesCode
     * @return Merchant
     */
    public function setSalesCode(string $salesCode): Merchant
    {
        $this->salesCode = $salesCode;
        return $this;
    }

    /**
     * @return Processing
     */
    public function getProcessing(): Processing
    {
        return $this->processing;
    }

    /**
     * @param Processing $processing
     * @return Merchant
     */
    public function setProcessing(Processing $processing): Merchant
    {
        $this->processing = $processing;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWebLeadFlg(): bool
    {
        return $this->webLeadFlg;
    }

    /**
     * @param bool $webLeadFlg
     * @return Merchant
     */
    public function setWebLeadFlg(bool $webLeadFlg): Merchant
    {
        $this->webLeadFlg = $webLeadFlg;
        return $this;
    }

    /**
     * @return Pricing
     */
    public function getPricing(): Pricing
    {
        return $this->pricing;
    }

    /**
     * @param Pricing $pricing
     * @return Merchant
     */
    public function setPricing(Pricing $pricing): Merchant
    {
        $this->pricing = $pricing;
        return $this;
    }

    /**
     * @return Fee
     */
    public function getFee(): Fee
    {
        return $this->fee;
    }

    /**
     * @param Fee $fee
     * @return Merchant
     */
    public function setFee(Fee $fee): Merchant
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    /**
     * @param array $customFields
     * @return Merchant
     */
    public function setCustomFields(array $customFields): Merchant
    {
        $this->customFields = $customFields;
        return $this;
    }
}
