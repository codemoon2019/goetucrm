<?php

namespace Guesl\CardConnect\CoPilot\Models;

class TransactionDetails extends Model
{
    /**
     * 0-100%
     * Required: yes
     * Size: 3
     *
     * @var int
     */
    protected $eCommercePct;

    /**
     * 0-100%
     * Required: yes
     * Size: 3
     *
     * @var int
     */
    protected $keyedPct;

    /**
     * 0-100%
     * Required: yes
     * Size: 3
     *
     * @var int
     */
    protected $mailOrderPct;

    /**
     * 0-100%
     * Required: yes
     * Size: 3
     *
     * @var int
     */
    protected $swipedPct;

    /**
     * @return int
     */
    public function getECommercePct(): int
    {
        return $this->eCommercePct;
    }

    /**
     * @param int $eCommercePct
     * @return TransactionDetails
     */
    public function setECommercePct(int $eCommercePct): TransactionDetails
    {
        $this->eCommercePct = $eCommercePct;
        return $this;
    }

    /**
     * @return int
     */
    public function getKeyedPct(): int
    {
        return $this->keyedPct;
    }

    /**
     * @param int $keyedPct
     * @return TransactionDetails
     */
    public function setKeyedPct(int $keyedPct): TransactionDetails
    {
        $this->keyedPct = $keyedPct;
        return $this;
    }

    /**
     * @return int
     */
    public function getMailOrderPct(): int
    {
        return $this->mailOrderPct;
    }

    /**
     * @param int $mailOrderPct
     * @return TransactionDetails
     */
    public function setMailOrderPct(int $mailOrderPct): TransactionDetails
    {
        $this->mailOrderPct = $mailOrderPct;
        return $this;
    }

    /**
     * @return int
     */
    public function getSwipedPct(): int
    {
        return $this->swipedPct;
    }

    /**
     * @param int $swipedPct
     * @return TransactionDetails
     */
    public function setSwipedPct(int $swipedPct): TransactionDetails
    {
        $this->swipedPct = $swipedPct;
        return $this;
    }
}
