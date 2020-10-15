<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use Guesl\CardConnect\CoPilot\Models\Attachment;
use Guesl\CardConnect\CoPilot\Models\EquipmentPageObject;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\SignatureStatus;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
interface EquipmentCatalogService
{
    /**
     * List the equipment available for a sales code.
     *
     * @param string|null $salesCode
     * @param string $equipmentSupplierCd
     * @param string $equipmentTypeCd
     * @param int $pageNumber
     * @param int $pageSize
     * @return EquipmentPageObject
     */
    public function fetch(string $salesCode = null, string $equipmentSupplierCd, string $equipmentTypeCd, int $pageNumber, int $pageSize);
}
