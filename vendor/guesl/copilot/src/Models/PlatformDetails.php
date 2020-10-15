<?php

namespace Guesl\CardConnect\CoPilot\Models;

class PlatformDetails extends Model
{
    /**
     * Required: no (yes for CardPointe only accounts)
     * Size: 25
     *
     * @var string
     */
    protected $backEndMid;

    /**
     * Required: no (yes for CardPointe only accounts)
     * Size: 25
     *
     * @var string
     */
    protected $frontEndMid;

    /**
     * Required: no (yes for CardPointe only accounts)
     * Size: 25
     *
     * @var string
     */
    protected $processorReportingMid;

    /**
     * https://developer.cardconnect.com/copilotapi#back-end-platform-enumeration-definition
     *
     * Required: no (yes for CardPointe only accounts)
     * Size: 20
     *
     * @var string
     */
    protected $backEndPlatform;

    /**
     * https://developer.cardconnect.com/copilotapi#front-end-platform-enumeration-definition
     *
     * Required: no (yes for CardPointe only accounts)
     * Size: 20
     *
     * @var string
     */
    protected $frontEndPlatformCd;

    /**
     * https://developer.cardconnect.com/copilotapi#amex-program-enumeration-definition
     * "American Express(AMEX) OptBlue" or "American Express(AMEX) ESA".
     *
     * Required: yes
     * Size: 7
     *
     * @var string
     */
    protected $amexProgramAssetCd;

    /**
     * Required: yes
     * Size: 25
     *
     * @var string
     */
    protected $amexProgramMid;

    /**
     * Required: yes
     * Size: 25
     *
     * @var string
     */
    protected $discoverMid;

    /**
     * https://developer.cardconnect.com/copilotapi#discover-program-enumeration-definition
     *
     * "External Agent Sales Incentive" or "Merchant Acquiring Program".
     *
     * Required: yes
     * Size: 10
     *
     * @var string
     */
    protected $discoverProgramCd;

    /**
     * true = Merchant Services, false = CardPointe only
     *
     * Required: yes
     *
     * @var boolean
     */
    protected $acquiringFlg;

    /**
     * Required: no
     * Size: 9
     *
     * @var integer
     */
    protected $taxId;

    /**
     * Required: no
     *
     * @var string
     */
    protected $tid;

    /**
     * Required: no
     *
     * @var string
     */
    protected $busnsId;

    /**
     * state code
     * https://developer.cardconnect.com/copilotapi#state-enumeration-definition
     *
     * Required: yes
     * Size: 2
     *
     * @var string
     */
    protected $busnsIdPlaceOfIssue;

    /**
     * Like "USD".
     *
     * Required: no
     * Size: 5
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * (numeric)
     *
     * Required: yes
     * Size: 5
     *
     * @var string
     */
    protected $mccId;

    /**
     * Required: yes
     * Size: 4000
     *
     * @var string
     */
    protected $businessDescription;

    /**
     * @return string
     */
    public function getBackEndMid(): string
    {
        return $this->backEndMid;
    }

    /**
     * @param string $backEndMid
     * @return PlatformDetails
     */
    public function setBackEndMid(string $backEndMid): PlatformDetails
    {
        $this->backEndMid = $backEndMid;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrontEndMid(): string
    {
        return $this->frontEndMid;
    }

    /**
     * @param string $frontEndMid
     * @return PlatformDetails
     */
    public function setFrontEndMid(string $frontEndMid): PlatformDetails
    {
        $this->frontEndMid = $frontEndMid;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorReportingMid(): string
    {
        return $this->processorReportingMid;
    }

    /**
     * @param string $processorReportingMid
     * @return PlatformDetails
     */
    public function setProcessorReportingMid(string $processorReportingMid): PlatformDetails
    {
        $this->processorReportingMid = $processorReportingMid;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackEndPlatform(): string
    {
        return $this->backEndPlatform;
    }

    /**
     * @param string $backEndPlatform
     * @return PlatformDetails
     */
    public function setBackEndPlatform(string $backEndPlatform): PlatformDetails
    {
        $this->backEndPlatform = $backEndPlatform;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrontEndPlatformCd(): string
    {
        return $this->frontEndPlatformCd;
    }

    /**
     * @param string $frontEndPlatformCd
     * @return PlatformDetails
     */
    public function setFrontEndPlatformCd(string $frontEndPlatformCd): PlatformDetails
    {
        $this->frontEndPlatformCd = $frontEndPlatformCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmexProgramAssetCd(): string
    {
        return $this->amexProgramAssetCd;
    }

    /**
     * @param string $amexProgramAssetCd
     * @return PlatformDetails
     */
    public function setAmexProgramAssetCd(string $amexProgramAssetCd): PlatformDetails
    {
        $this->amexProgramAssetCd = $amexProgramAssetCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmexProgramMid(): string
    {
        return $this->amexProgramMid;
    }

    /**
     * @param string $amexProgramMid
     * @return PlatformDetails
     */
    public function setAmexProgramMid(string $amexProgramMid): PlatformDetails
    {
        $this->amexProgramMid = $amexProgramMid;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscoverMid(): string
    {
        return $this->discoverMid;
    }

    /**
     * @param string $discoverMid
     * @return PlatformDetails
     */
    public function setDiscoverMid(string $discoverMid): PlatformDetails
    {
        $this->discoverMid = $discoverMid;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscoverProgramCd(): string
    {
        return $this->discoverProgramCd;
    }

    /**
     * @param string $discoverProgramCd
     * @return PlatformDetails
     */
    public function setDiscoverProgramCd(string $discoverProgramCd): PlatformDetails
    {
        $this->discoverProgramCd = $discoverProgramCd;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAcquiringFlg(): bool
    {
        return $this->acquiringFlg;
    }

    /**
     * @param bool $acquiringFlg
     * @return PlatformDetails
     */
    public function setAcquiringFlg(bool $acquiringFlg): PlatformDetails
    {
        $this->acquiringFlg = $acquiringFlg;
        return $this;
    }

    /**
     * @return int
     */
    public function getTaxId(): int
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     * @return PlatformDetails
     */
    public function setTaxId(int $taxId): PlatformDetails
    {
        $this->taxId = $taxId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTid(): string
    {
        return $this->tid;
    }

    /**
     * @param string $tid
     * @return PlatformDetails
     */
    public function setTid(string $tid): PlatformDetails
    {
        $this->tid = $tid;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusnsId(): string
    {
        return $this->busnsId;
    }

    /**
     * @param string $busnsId
     * @return PlatformDetails
     */
    public function setBusnsId(string $busnsId): PlatformDetails
    {
        $this->busnsId = $busnsId;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusnsIdPlaceOfIssue(): string
    {
        return $this->busnsIdPlaceOfIssue;
    }

    /**
     * @param string $busnsIdPlaceOfIssue
     * @return PlatformDetails
     */
    public function setBusnsIdPlaceOfIssue(string $busnsIdPlaceOfIssue): PlatformDetails
    {
        $this->busnsIdPlaceOfIssue = $busnsIdPlaceOfIssue;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     * @return PlatformDetails
     */
    public function setCurrencyCode(string $currencyCode): PlatformDetails
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMccId(): string
    {
        return $this->mccId;
    }

    /**
     * @param string $mccId
     * @return PlatformDetails
     */
    public function setMccId(string $mccId): PlatformDetails
    {
        $this->mccId = $mccId;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessDescription(): string
    {
        return $this->businessDescription;
    }

    /**
     * @param string $businessDescription
     * @return PlatformDetails
     */
    public function setBusinessDescription(string $businessDescription): PlatformDetails
    {
        $this->businessDescription = $businessDescription;
        return $this;
    }
}
