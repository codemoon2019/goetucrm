<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Processing extends Model
{
    /**
     * Required: yes
     *
     * @var PlatformDetails
     */
    protected $platformDetails;

    /**
     * Required: yes
     *
     * @var BusinessDetails
     */
    protected $businessDetails;

    /**
     * Required: yes
     *
     * @var VolumeDetails
     */
    protected $volumeDetails;

    /**
     * Required: yes
     *
     * @var DeliveryDetails
     */
    protected $deliveryPercentages;

    /**
     * Required: yes
     *
     * @var TransactionDetails
     */
    protected $modeOfTransaction;

    /**
     * @return PlatformDetails
     */
    public function getPlatformDetails(): PlatformDetails
    {
        return $this->platformDetails;
    }

    /**
     * @param PlatformDetails $platformDetails
     * @return Processing
     */
    public function setPlatformDetails(PlatformDetails $platformDetails): Processing
    {
        $this->platformDetails = $platformDetails;
        return $this;
    }

    /**
     * @return BusinessDetails
     */
    public function getBusinessDetails(): BusinessDetails
    {
        return $this->businessDetails;
    }

    /**
     * @param BusinessDetails $businessDetails
     * @return Processing
     */
    public function setBusinessDetails(BusinessDetails $businessDetails): Processing
    {
        $this->businessDetails = $businessDetails;
        return $this;
    }

    /**
     * @return VolumeDetails
     */
    public function getVolumeDetails(): VolumeDetails
    {
        return $this->volumeDetails;
    }

    /**
     * @param VolumeDetails $volumeDetails
     * @return Processing
     */
    public function setVolumeDetails(VolumeDetails $volumeDetails): Processing
    {
        $this->volumeDetails = $volumeDetails;
        return $this;
    }

    /**
     * @return DeliveryDetails
     */
    public function getDeliveryPercentages(): DeliveryDetails
    {
        return $this->deliveryPercentages;
    }

    /**
     * @param DeliveryDetails $deliveryPercentages
     * @return Processing
     */
    public function setDeliveryPercentages(DeliveryDetails $deliveryPercentages): Processing
    {
        $this->deliveryPercentages = $deliveryPercentages;
        return $this;
    }

    /**
     * @return TransactionDetails
     */
    public function getModeOfTransaction(): TransactionDetails
    {
        return $this->modeOfTransaction;
    }

    /**
     * @param TransactionDetails $modeOfTransaction
     * @return Processing
     */
    public function setModeOfTransaction(TransactionDetails $modeOfTransaction): Processing
    {
        $this->modeOfTransaction = $modeOfTransaction;
        return $this;
    }

}
