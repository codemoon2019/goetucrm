<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Equipment extends Model
{
    /**
     * The equipment ID
     * Size: 60
     *
     * @var int
     */
    protected $equipmentId;

    /**
     * The equipment name
     * Size: 60
     *
     * @var string
     */
    protected $equipmentName;

    /**
     * The equipment description
     * Size: 60
     *
     * @var string
     */
    protected $description;

    /**
     * The equipment make
     * Size: 60
     *
     * @var string
     */
    protected $make;

    /**
     * The equipment model
     * Size: 60
     *
     * @var string
     */
    protected $model;

    /**
     * The equipment image url
     * Size: 60
     *
     * @var string
     */
    protected $imageUrl;

    /**
     * The equipment sku
     * Required: yes
     * Size: 60
     *
     * @var string
     */
    protected $sku;

    /**
     * The equipment platform
     * Size: 60
     *
     * @var string
     */
    protected $platformCd;

    /**
     * The equipment type
     * https://developer.cardconnect.com/copilotapi#equipment-type-enumeration-definition
     *
     * "Terminal", "Gateway", or "Software".
     *
     * @var string
     */
    protected $equipmentTypeCd;

    /**
     * The equipment supplier code
     * https://developer.cardconnect.com/copilotapi#equipment-supplier-codes
     *
     * "First Data Marketplace", "CardConnect", "Thirdparty", "TMS".
     *
     * @var string
     */
    protected $quipmentSupplierCd;

    /**
     * The default one time price
     * Size: 60
     *
     * @var string
     */
    protected $defaultPrice;

    /**
     * The default monthly price if allowed
     * Size: 60
     *
     * @var string
     */
    protected $defaultPlanPrice;

    /**
     * True if monthly billing is allowed
     * Size: 60
     *
     * @var string
     */
    protected $allowCustomBillingFlg;

    /**
     * The standard shipping price (per unit)
     * Size: 60
     *
     * @var string
     */
    protected $standardShippingPrice;

    /**
     * The expedited shipping price (per unit)
     * Size: 60
     *
     * @var string
     */
    protected $expeditedShippingPrice;

    /**
     * @return int
     */
    public function getEquipmentId(): int
    {
        return $this->equipmentId;
    }

    /**
     * @param int $equipmentId
     * @return Equipment
     */
    public function setEquipmentId(int $equipmentId): Equipment
    {
        $this->equipmentId = $equipmentId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEquipmentName(): string
    {
        return $this->equipmentName;
    }

    /**
     * @param string $equipmentName
     * @return Equipment
     */
    public function setEquipmentName(string $equipmentName): Equipment
    {
        $this->equipmentName = $equipmentName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Equipment
     */
    public function setDescription(string $description): Equipment
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getMake(): string
    {
        return $this->make;
    }

    /**
     * @param string $make
     * @return Equipment
     */
    public function setMake(string $make): Equipment
    {
        $this->make = $make;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return Equipment
     */
    public function setModel(string $model): Equipment
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     * @return Equipment
     */
    public function setImageUrl(string $imageUrl): Equipment
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return Equipment
     */
    public function setSku(string $sku): Equipment
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlatformCd(): string
    {
        return $this->platformCd;
    }

    /**
     * @param string $platformCd
     * @return Equipment
     */
    public function setPlatformCd(string $platformCd): Equipment
    {
        $this->platformCd = $platformCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getEquipmentTypeCd(): string
    {
        return $this->equipmentTypeCd;
    }

    /**
     * @param string $equipmentTypeCd
     * @return Equipment
     */
    public function setEquipmentTypeCd(string $equipmentTypeCd): Equipment
    {
        $this->equipmentTypeCd = $equipmentTypeCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuipmentSupplierCd(): string
    {
        return $this->quipmentSupplierCd;
    }

    /**
     * @param string $quipmentSupplierCd
     * @return Equipment
     */
    public function setQuipmentSupplierCd(string $quipmentSupplierCd): Equipment
    {
        $this->quipmentSupplierCd = $quipmentSupplierCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultPrice(): string
    {
        return $this->defaultPrice;
    }

    /**
     * @param string $defaultPrice
     * @return Equipment
     */
    public function setDefaultPrice(string $defaultPrice): Equipment
    {
        $this->defaultPrice = $defaultPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultPlanPrice(): string
    {
        return $this->defaultPlanPrice;
    }

    /**
     * @param string $defaultPlanPrice
     * @return Equipment
     */
    public function setDefaultPlanPrice(string $defaultPlanPrice): Equipment
    {
        $this->defaultPlanPrice = $defaultPlanPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getAllowCustomBillingFlg(): string
    {
        return $this->allowCustomBillingFlg;
    }

    /**
     * @param string $allowCustomBillingFlg
     * @return Equipment
     */
    public function setAllowCustomBillingFlg(string $allowCustomBillingFlg): Equipment
    {
        $this->allowCustomBillingFlg = $allowCustomBillingFlg;
        return $this;
    }

    /**
     * @return string
     */
    public function getStandardShippingPrice(): string
    {
        return $this->standardShippingPrice;
    }

    /**
     * @param string $standardShippingPrice
     * @return Equipment
     */
    public function setStandardShippingPrice(string $standardShippingPrice): Equipment
    {
        $this->standardShippingPrice = $standardShippingPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpeditedShippingPrice(): string
    {
        return $this->expeditedShippingPrice;
    }

    /**
     * @param string $expeditedShippingPrice
     * @return Equipment
     */
    public function setExpeditedShippingPrice(string $expeditedShippingPrice): Equipment
    {
        $this->expeditedShippingPrice = $expeditedShippingPrice;
        return $this;
    }
}
