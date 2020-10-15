<?php

namespace Guesl\CardConnect\CoPilot\Models;

class VolumeDetails extends Model
{
    /**
     * Required: yes
     *
     * @var float
     */
    protected $averageMonthlyVolume;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $highTicketAmount;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $averageTicketAmount;

    /**
     * @return float
     */
    public function getAverageMonthlyVolume(): float
    {
        return $this->averageMonthlyVolume;
    }

    /**
     * @param float $averageMonthlyVolume
     * @return VolumeDetails
     */
    public function setAverageMonthlyVolume(float $averageMonthlyVolume): VolumeDetails
    {
        $this->averageMonthlyVolume = $averageMonthlyVolume;
        return $this;
    }

    /**
     * @return float
     */
    public function getHighTicketAmount(): float
    {
        return $this->highTicketAmount;
    }

    /**
     * @param float $highTicketAmount
     * @return VolumeDetails
     */
    public function setHighTicketAmount(float $highTicketAmount): VolumeDetails
    {
        $this->highTicketAmount = $highTicketAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAverageTicketAmount(): float
    {
        return $this->averageTicketAmount;
    }

    /**
     * @param float $averageTicketAmount
     * @return VolumeDetails
     */
    public function setAverageTicketAmount(float $averageTicketAmount): VolumeDetails
    {
        $this->averageTicketAmount = $averageTicketAmount;
        return $this;
    }
}
