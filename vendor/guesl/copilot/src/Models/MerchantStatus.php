<?php

namespace Guesl\CardConnect\CoPilot\Models;

class MerchantStatus extends Model
{
    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $approvedDatetime;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $boardedDatetime;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $cancelledDatetime;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $declinedDatetime;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $liveDatetime;

    /**
     * @var bool
     */
    protected $boardingProcessPendingFlg;

    /**
     * @var string
     */
    protected $boardingProcessStatusCd;

    /**
     * @var string
     */
    protected $gatewayBoardingStatusCd;

    /**
     * @return string
     */
    public function getApprovedDatetime(): string
    {
        return $this->approvedDatetime;
    }

    /**
     * @param string $approvedDatetime
     * @return MerchantStatus
     */
    public function setApprovedDatetime(string $approvedDatetime): MerchantStatus
    {
        $this->approvedDatetime = $approvedDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getBoardedDatetime(): string
    {
        return $this->boardedDatetime;
    }

    /**
     * @param string $boardedDatetime
     * @return MerchantStatus
     */
    public function setBoardedDatetime(string $boardedDatetime): MerchantStatus
    {
        $this->boardedDatetime = $boardedDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getCancelledDatetime(): string
    {
        return $this->cancelledDatetime;
    }

    /**
     * @param string $cancelledDatetime
     * @return MerchantStatus
     */
    public function setCancelledDatetime(string $cancelledDatetime): MerchantStatus
    {
        $this->cancelledDatetime = $cancelledDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeclinedDatetime(): string
    {
        return $this->declinedDatetime;
    }

    /**
     * @param string $declinedDatetime
     * @return MerchantStatus
     */
    public function setDeclinedDatetime(string $declinedDatetime): MerchantStatus
    {
        $this->declinedDatetime = $declinedDatetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getLiveDatetime(): string
    {
        return $this->liveDatetime;
    }

    /**
     * @param string $liveDatetime
     * @return MerchantStatus
     */
    public function setLiveDatetime(string $liveDatetime): MerchantStatus
    {
        $this->liveDatetime = $liveDatetime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBoardingProcessPendingFlg(): bool
    {
        return $this->boardingProcessPendingFlg;
    }

    /**
     * @param bool $boardingProcessPendingFlg
     * @return MerchantStatus
     */
    public function setBoardingProcessPendingFlg(bool $boardingProcessPendingFlg): MerchantStatus
    {
        $this->boardingProcessPendingFlg = $boardingProcessPendingFlg;
        return $this;
    }

    /**
     * @return string
     */
    public function getBoardingProcessStatusCd(): string
    {
        return $this->boardingProcessStatusCd;
    }

    /**
     * @param string $boardingProcessStatusCd
     * @return MerchantStatus
     */
    public function setBoardingProcessStatusCd(string $boardingProcessStatusCd): MerchantStatus
    {
        $this->boardingProcessStatusCd = $boardingProcessStatusCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getGatewayBoardingStatusCd(): string
    {
        return $this->gatewayBoardingStatusCd;
    }

    /**
     * @param string $gatewayBoardingStatusCd
     * @return MerchantStatus
     */
    public function setGatewayBoardingStatusCd(string $gatewayBoardingStatusCd): MerchantStatus
    {
        $this->gatewayBoardingStatusCd = $gatewayBoardingStatusCd;
        return $this;
    }
}
