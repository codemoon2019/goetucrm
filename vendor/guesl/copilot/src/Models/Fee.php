<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Fee extends Model
{
    /**
     * Required: yes
     *
     * @var float
     */
    protected $achBatchFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $addressVerifFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $annualMembershipFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $appFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $authFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $chargebackFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $dataBreachFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $ddaRejectFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $earlyCancelFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $minProcessFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $monthlyEquipmentRentalFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $pciAnnualFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $pciNonComplianceFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $regProdMonthlyFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $retrievalFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $statementFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $transactionFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $voiceAuthFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $wirelessActivationFee;

    /**
     * Required: yes
     *
     * @var float
     */
    protected $wirelessFee;

    /**
     * Required: no
     *
     * @var bool
     */
    protected $duesAndAssessmentsFlg;

    /**
     * Required: no
     *
     * @var bool
     */
    protected $passthruInterchgCostsFlg;

    /**
     * @return float
     */
    public function getAchBatchFee(): float
    {
        return $this->achBatchFee;
    }

    /**
     * @param float $achBatchFee
     * @return Fee
     */
    public function setAchBatchFee(float $achBatchFee): Fee
    {
        $this->achBatchFee = $achBatchFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getAddressVerifFee(): float
    {
        return $this->addressVerifFee;
    }

    /**
     * @param float $addressVerifFee
     * @return Fee
     */
    public function setAddressVerifFee(float $addressVerifFee): Fee
    {
        $this->addressVerifFee = $addressVerifFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getAnnualMembershipFee(): float
    {
        return $this->annualMembershipFee;
    }

    /**
     * @param float $annualMembershipFee
     * @return Fee
     */
    public function setAnnualMembershipFee(float $annualMembershipFee): Fee
    {
        $this->annualMembershipFee = $annualMembershipFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getAppFee(): float
    {
        return $this->appFee;
    }

    /**
     * @param float $appFee
     * @return Fee
     */
    public function setAppFee(float $appFee): Fee
    {
        $this->appFee = $appFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getAuthFee(): float
    {
        return $this->authFee;
    }

    /**
     * @param float $authFee
     * @return Fee
     */
    public function setAuthFee(float $authFee): Fee
    {
        $this->authFee = $authFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getChargebackFee(): float
    {
        return $this->chargebackFee;
    }

    /**
     * @param float $chargebackFee
     * @return Fee
     */
    public function setChargebackFee(float $chargebackFee): Fee
    {
        $this->chargebackFee = $chargebackFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getDataBreachFee(): float
    {
        return $this->dataBreachFee;
    }

    /**
     * @param float $dataBreachFee
     * @return Fee
     */
    public function setDataBreachFee(float $dataBreachFee): Fee
    {
        $this->dataBreachFee = $dataBreachFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getDdaRejectFee(): float
    {
        return $this->ddaRejectFee;
    }

    /**
     * @param float $ddaRejectFee
     * @return Fee
     */
    public function setDdaRejectFee(float $ddaRejectFee): Fee
    {
        $this->ddaRejectFee = $ddaRejectFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getEarlyCancelFee(): float
    {
        return $this->earlyCancelFee;
    }

    /**
     * @param float $earlyCancelFee
     * @return Fee
     */
    public function setEarlyCancelFee(float $earlyCancelFee): Fee
    {
        $this->earlyCancelFee = $earlyCancelFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getMinProcessFee(): float
    {
        return $this->minProcessFee;
    }

    /**
     * @param float $minProcessFee
     * @return Fee
     */
    public function setMinProcessFee(float $minProcessFee): Fee
    {
        $this->minProcessFee = $minProcessFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getMonthlyEquipmentRentalFee(): float
    {
        return $this->monthlyEquipmentRentalFee;
    }

    /**
     * @param float $monthlyEquipmentRentalFee
     * @return Fee
     */
    public function setMonthlyEquipmentRentalFee(float $monthlyEquipmentRentalFee): Fee
    {
        $this->monthlyEquipmentRentalFee = $monthlyEquipmentRentalFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getPciAnnualFee(): float
    {
        return $this->pciAnnualFee;
    }

    /**
     * @param float $pciAnnualFee
     * @return Fee
     */
    public function setPciAnnualFee(float $pciAnnualFee): Fee
    {
        $this->pciAnnualFee = $pciAnnualFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getPciNonComplianceFee(): float
    {
        return $this->pciNonComplianceFee;
    }

    /**
     * @param float $pciNonComplianceFee
     * @return Fee
     */
    public function setPciNonComplianceFee(float $pciNonComplianceFee): Fee
    {
        $this->pciNonComplianceFee = $pciNonComplianceFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getRegProdMonthlyFee(): float
    {
        return $this->regProdMonthlyFee;
    }

    /**
     * @param float $regProdMonthlyFee
     * @return Fee
     */
    public function setRegProdMonthlyFee(float $regProdMonthlyFee): Fee
    {
        $this->regProdMonthlyFee = $regProdMonthlyFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getRetrievalFee(): float
    {
        return $this->retrievalFee;
    }

    /**
     * @param float $retrievalFee
     * @return Fee
     */
    public function setRetrievalFee(float $retrievalFee): Fee
    {
        $this->retrievalFee = $retrievalFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getStatementFee(): float
    {
        return $this->statementFee;
    }

    /**
     * @param float $statementFee
     * @return Fee
     */
    public function setStatementFee(float $statementFee): Fee
    {
        $this->statementFee = $statementFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getTransactionFee(): float
    {
        return $this->transactionFee;
    }

    /**
     * @param float $transactionFee
     * @return Fee
     */
    public function setTransactionFee(float $transactionFee): Fee
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getVoiceAuthFee(): float
    {
        return $this->voiceAuthFee;
    }

    /**
     * @param float $voiceAuthFee
     * @return Fee
     */
    public function setVoiceAuthFee(float $voiceAuthFee): Fee
    {
        $this->voiceAuthFee = $voiceAuthFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getWirelessActivationFee(): float
    {
        return $this->wirelessActivationFee;
    }

    /**
     * @param float $wirelessActivationFee
     * @return Fee
     */
    public function setWirelessActivationFee(float $wirelessActivationFee): Fee
    {
        $this->wirelessActivationFee = $wirelessActivationFee;
        return $this;
    }

    /**
     * @return float
     */
    public function getWirelessFee(): float
    {
        return $this->wirelessFee;
    }

    /**
     * @param float $wirelessFee
     * @return Fee
     */
    public function setWirelessFee(float $wirelessFee): Fee
    {
        $this->wirelessFee = $wirelessFee;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDuesAndAssessmentsFlg(): bool
    {
        return $this->duesAndAssessmentsFlg;
    }

    /**
     * @param bool $duesAndAssessmentsFlg
     * @return Fee
     */
    public function setDuesAndAssessmentsFlg(bool $duesAndAssessmentsFlg): Fee
    {
        $this->duesAndAssessmentsFlg = $duesAndAssessmentsFlg;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPassthruInterchgCostsFlg(): bool
    {
        return $this->passthruInterchgCostsFlg;
    }

    /**
     * @param bool $passthruInterchgCostsFlg
     * @return Fee
     */
    public function setPassthruInterchgCostsFlg(bool $passthruInterchgCostsFlg): Fee
    {
        $this->passthruInterchgCostsFlg = $passthruInterchgCostsFlg;
        return $this;
    }
}
