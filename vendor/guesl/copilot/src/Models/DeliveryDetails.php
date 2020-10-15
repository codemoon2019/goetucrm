<?php

namespace Guesl\CardConnect\CoPilot\Models;

class DeliveryDetails extends Model
{
    /**
     * Required: no
     * Size: 3
     *
     * @var int
     */
    protected $dlvry0To7DaysPct;

    /**
     * Required: no
     * Size: 3
     *
     * @var int
     */
    protected $dlvry15To30DaysPct;

    /**
     * Required: no
     * Size: 3
     *
     * @var int
     */
    protected $dlvry8To14DaysPct;

    /**
     * Required: no
     * Size: 3
     *
     * @var int
     */
    protected $dlvryOver30DaysPct;

    /**
     * @return int
     */
    public function getDlvry0To7DaysPct(): int
    {
        return $this->dlvry0To7DaysPct;
    }

    /**
     * @param int $dlvry0To7DaysPct
     * @return DeliveryDetails
     */
    public function setDlvry0To7DaysPct(int $dlvry0To7DaysPct): DeliveryDetails
    {
        $this->dlvry0To7DaysPct = $dlvry0To7DaysPct;
        return $this;
    }

    /**
     * @return int
     */
    public function getDlvry15To30DaysPct(): int
    {
        return $this->dlvry15To30DaysPct;
    }

    /**
     * @param int $dlvry15To30DaysPct
     * @return DeliveryDetails
     */
    public function setDlvry15To30DaysPct(int $dlvry15To30DaysPct): DeliveryDetails
    {
        $this->dlvry15To30DaysPct = $dlvry15To30DaysPct;
        return $this;
    }

    /**
     * @return int
     */
    public function getDlvry8To14DaysPct(): int
    {
        return $this->dlvry8To14DaysPct;
    }

    /**
     * @param int $dlvry8To14DaysPct
     * @return DeliveryDetails
     */
    public function setDlvry8To14DaysPct(int $dlvry8To14DaysPct): DeliveryDetails
    {
        $this->dlvry8To14DaysPct = $dlvry8To14DaysPct;
        return $this;
    }

    /**
     * @return int
     */
    public function getDlvryOver30DaysPct(): int
    {
        return $this->dlvryOver30DaysPct;
    }

    /**
     * @param int $dlvryOver30DaysPct
     * @return DeliveryDetails
     */
    public function setDlvryOver30DaysPct(int $dlvryOver30DaysPct): DeliveryDetails
    {
        $this->dlvryOver30DaysPct = $dlvryOver30DaysPct;
        return $this;
    }
}
