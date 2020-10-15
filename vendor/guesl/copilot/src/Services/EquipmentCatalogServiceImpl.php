<?php

namespace Guesl\CardConnect\CoPilot\Services;

use Guesl\CardConnect\CoPilot\Contracts\AbstractBaseService;
use Guesl\CardConnect\CoPilot\Contracts\EquipmentCatalogService;
use Guesl\CardConnect\CoPilot\Contracts\OwnerSiteUserService;
use Guesl\CardConnect\CoPilot\Models\EquipmentPageObject;
use Guesl\CardConnect\CoPilot\Models\Owner;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Created by Jianfeng Li.
 * User => Jianfeng Li
 * Date => 2018/05/09
 */
class EquipmentCatalogServiceImpl extends AbstractBaseService implements EquipmentCatalogService
{
    /**
     * CoPilot Merchant sale code.
     *
     * @var string
     */
    protected $salesCode;

    /**
     * CoPilot Equipment catalog url.
     *
     * @var string
     */
    private $url = "/equipmentcatalog/list";

    /**
     * List the equipment available for a sales code.
     *
     * @param string $salesCode
     * @param string $equipmentSupplierCd
     * @param string $equipmentTypeCd
     * @param int $pageNumber
     * @param int $pageSize
     * @return EquipmentPageObject
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function fetch(string $salesCode = null, string $equipmentSupplierCd, string $equipmentTypeCd, int $pageNumber, int $pageSize)
    {
        Log::debug(get_class($this) . "::fetch => List the equipment available for a sales code.");

        if (!isset($salesCode)) {
            $salesCode = $this->salesCode;
        }

        $options = $this->getRequestHeader();
        $parameters = [
            "salesCode" => $salesCode,
            "equipmentSupplierCd" => $equipmentSupplierCd,
            "equipmentTypeCd" => $equipmentTypeCd,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
        ];
        $options["query"] = $parameters;

        try {
            $response = $this->httpClient->get($this->url, $options);
            dd($response);
            $equipmentPageObject = (new EquipmentPageObject())->populate($response);
            return $equipmentPageObject;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }
}
