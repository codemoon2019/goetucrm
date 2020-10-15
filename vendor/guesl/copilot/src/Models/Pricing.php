<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Pricing extends Model
{
    /**
     * Required: yes
     *
     * @var FlatPricing
     */
    protected $flatPricing;

    /**
     * Required: yes
     *
     * @var ICPlusPricing
     */
    protected $icPlusPricing;

    /**
     * @return FlatPricing
     */
    public function getFlatPricing(): FlatPricing
    {
        return $this->flatPricing;
    }

    /**
     * @param FlatPricing $flatPricing
     * @return Pricing
     */
    public function setFlatPricing(FlatPricing $flatPricing): Pricing
    {
        $this->flatPricing = $flatPricing;
        return $this;
    }

    /**
     * @return ICPlusPricing
     */
    public function getIcPlusPricing(): ICPlusPricing
    {
        return $this->icPlusPricing;
    }

    /**
     * @param ICPlusPricing $icPlusPricing
     * @return Pricing
     */
    public function setIcPlusPricing(ICPlusPricing $icPlusPricing): TYPE_NAME
    {
        $this->icPlusPricing = $icPlusPricing;
        return $this;
    }
}
