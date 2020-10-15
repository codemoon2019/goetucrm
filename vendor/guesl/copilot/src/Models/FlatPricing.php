<?php

namespace Guesl\CardConnect\CoPilot\Models;

class FlatPricing extends Model
{
    /**
     * Only valid if amexProgramAssetCd is ESA
     *
     * Required: yes
     *
     * @var float
     */
    protected $amexEsaQualDiscountPct;

    /**
     * Only valid if amexProgramAssetCd is ESA
     *
     * Required: yes
     *
     * @var float
     */
    protected $amexOptBlueQualDiscountPct;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $discoverQualCreditDiscountPct;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $mastercardQualCreditDiscountPct;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $visaQualCreditDiscountPct;

    /**
     * @return float
     */
    public function getAmexEsaQualDiscountPct(): float
    {
        return $this->amexEsaQualDiscountPct;
    }

    /**
     * @param float $amexEsaQualDiscountPct
     * @return FlatPricing
     */
    public function setAmexEsaQualDiscountPct(float $amexEsaQualDiscountPct): FlatPricing
    {
        $this->amexEsaQualDiscountPct = $amexEsaQualDiscountPct;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmexOptBlueQualDiscountPct(): float
    {
        return $this->amexOptBlueQualDiscountPct;
    }

    /**
     * @param float $amexOptBlueQualDiscountPct
     * @return FlatPricing
     */
    public function setAmexOptBlueQualDiscountPct(float $amexOptBlueQualDiscountPct): FlatPricing
    {
        $this->amexOptBlueQualDiscountPct = $amexOptBlueQualDiscountPct;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscoverQualCreditDiscountPct(): float
    {
        return $this->discoverQualCreditDiscountPct;
    }

    /**
     * @param float $discoverQualCreditDiscountPct
     * @return FlatPricing
     */
    public function setDiscoverQualCreditDiscountPct(float $discoverQualCreditDiscountPct): FlatPricing
    {
        $this->discoverQualCreditDiscountPct = $discoverQualCreditDiscountPct;
        return $this;
    }

    /**
     * @return float
     */
    public function getMastercardQualCreditDiscountPct(): float
    {
        return $this->mastercardQualCreditDiscountPct;
    }

    /**
     * @param float $mastercardQualCreditDiscountPct
     * @return FlatPricing
     */
    public function setMastercardQualCreditDiscountPct(float $mastercardQualCreditDiscountPct): FlatPricing
    {
        $this->mastercardQualCreditDiscountPct = $mastercardQualCreditDiscountPct;
        return $this;
    }

    /**
     * @return float
     */
    public function getVisaQualCreditDiscountPct(): float
    {
        return $this->visaQualCreditDiscountPct;
    }

    /**
     * @param float $visaQualCreditDiscountPct
     * @return FlatPricing
     */
    public function setVisaQualCreditDiscountPct(float $visaQualCreditDiscountPct): FlatPricing
    {
        $this->visaQualCreditDiscountPct = $visaQualCreditDiscountPct;
        return $this;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            "amex" => [
                "esaQualDiscountPct" => $this->amexEsaQualDiscountPct,
                "optBlueQualDiscountPct" => $this->amexOptBlueQualDiscountPct,
            ],
            "discover" => [
                "qualCreditDiscountPct" => $this->discoverQualCreditDiscountPct,
            ],
            "mastercard" => [
                "qualCreditDiscountPct" => $this->mastercardQualCreditDiscountPct,
            ],
            "visa" => [
                "qualCreditDiscountPct" => $this->visaQualCreditDiscountPct,
            ]
        ];
    }

    /**
     * Populate the object by the values array.
     *
     * @param array $values
     * @return $this
     */
    public function populate(array $values)
    {
        if (array_key_exists("amex", $values) && array_key_exists("esaQualDiscountPct", $values["amex"])) {
            $this->amexEsaQualDiscountPct = $values["amex"]["esaQualDiscountPct"];
        }
        if (array_key_exists("amex", $values) && array_key_exists("optBlueQualDiscountPct", $values["amex"])) {
            $this->amexOptBlueQualDiscountPct = $values["amex"]["optBlueQualDiscountPct"];
        }
        if (array_key_exists("discover", $values) && array_key_exists("qualCreditDiscountPct", $values["amex"])) {
            $this->discoverQualCreditDiscountPct = $values["discover"]["qualCreditDiscountPct"];
        }
        if (array_key_exists("mastercard", $values) && array_key_exists("qualCreditDiscountPct", $values["amex"])) {
            $this->mastercardQualCreditDiscountPct = $values["mastercard"]["qualCreditDiscountPct"];
        }
        if (array_key_exists("visa", $values) && array_key_exists("qualCreditDiscountPct", $values["amex"])) {
            $this->visaQualCreditDiscountPct = $values["visa"]["qualCreditDiscountPct"];
        }
        return $this;
    }
}
