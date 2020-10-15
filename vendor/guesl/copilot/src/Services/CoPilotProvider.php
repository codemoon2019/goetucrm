<?php

namespace Guesl\CardConnect\CoPilot\Services;

use Guesl\CardConnect\CoPilot\Contracts\CoPilotService;
use Guesl\CardConnect\CoPilot\Contracts\MerchantService;
use Guesl\CardConnect\CoPilot\Contracts\OAuthService;
use Guesl\CardConnect\CoPilot\Contracts\OwnerSiteUserService;
use Guesl\CardConnect\CoPilot\Models\Attachment;
use Guesl\CardConnect\CoPilot\Models\EquipmentPageObject;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\MerchantStatus;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\SignatureStatus;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/04/13
 */
class CoPilotProvider implements CoPilotService
{
    /**
     * MerchantService
     *
     * @var OAuthService
     */
    private $oauthService;

    /**
     * MerchantService
     *
     * @var MerchantService
     */
    private $merchantService;

    /**
     * OwnerSiteUserService
     *
     * @var OwnerSiteUserService
     */
    private $ownerSiteUserService;

    /**
     * OwnerSiteUserService
     *
     * @var OwnerSiteUserService
     */
    private $equipmentCatalogService;

    /**
     * CoPilotProvider constructor.
     * @param $config
     * @var array
     */
    public function __construct($config)
    {
        $this->oauthService = new OAuthServiceImpl($config);
        $this->merchantService = new MerchantServiceImpl($config);
        $this->ownerSiteUserService = new OwnerSiteUserServiceImpl($config);
        $this->equipmentCatalogService = new EquipmentCatalogServiceImpl($config);
    }

    /**
     * Create access token.
     *
     * @return mixed
     */
    public function createAccessToken()
    {
        $response = $this->oauthService->createAccessToken();
        return $response;
    }

    /**
     * Refresh access token.
     *
     * @param $refreshToken
     * @return mixed
     */
    public function refreshAccessToken($refreshToken)
    {
        $response = $this->oauthService->refreshAccessToken($refreshToken);
        return $response;
    }

    /**
     * Create Merchant.
     *
     * @param int $templateId
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return int
     */
    public function createMerchant(int $templateId, Merchant $merchant, OwnerSiteUser $ownerUser = null)
    {
        $response = $this->merchantService->create($templateId, $merchant, $ownerUser);
        return $response;
    }

    /**
     * Update merchant.
     *
     * @param int $id
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function updateMerchant(int $id, Merchant $merchant, OwnerSiteUser $ownerUser = null)
    {
        $response = $this->merchantService->update($id, $merchant, $ownerUser);
        return $response;
    }

    /**
     * Retrieve merchant.
     *
     * @param int $id
     * @return Merchant
     */
    public function retrieveMerchant(int $id)
    {
        $response = $this->merchantService->retrieve($id);
        return $response;
    }

    /**
     * Retrieve merchant status.
     *
     * @param int $id
     * @return MerchantStatus
     */
    public function retrieveMerchantStatus(int $id)
    {
        $response = $this->merchantService->retrieveStatus($id);
        return $response;
    }

    /**
     * Request Merchant Signature.
     *
     * If the request signature was successful, the signature url will be returned.
     *
     * @param int $id
     * @return string
     */
    public function signMerchant(int $id)
    {
        $result = $this->merchantService->sign($id);
        return $result;
    }

    /**
     * Request merchant signature status.
     *
     * @param int $id
     * @return SignatureStatus
     */
    public function retrieveMerchantSignatureStatus($id)
    {
        $result = $this->merchantService->retrieveSignatureStatus($id);
        return $result;
    }

    /**
     * Put the merchant back in the In Progress state.
     *
     * @param $id
     */
    public function retractMerchantSignature($id)
    {
        $result = $this->merchantService->retractSignature($id);
        return $result;
    }

    /**
     * Create a merchant attachment.
     *
     * @param $id
     * @param Attachment $attachment
     * @return int
     */
    public function createMerchantAttachment($id, Attachment $attachment)
    {
        $result = $this->merchantService->createAttachment($id, $attachment);
        return $result;
    }

    /**
     * Create owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser $ownerUser
     * @return mixed
     */
    public function createOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser)
    {
        $response = $this->ownerSiteUserService->create($merchantId, $ownerUser);
        return $response;
    }

    /**
     * Update owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function updateOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser = null)
    {
        $response = $this->ownerSiteUserService->update($merchantId, $ownerUser);
        return $response;
    }

    /**
     * Retrieve owner site user of the merchant.
     *
     * @param int $merchantId
     * @return OwnerSiteUser
     */
    public function retrieveOwnerSiteUser(int $merchantId)
    {
        $response = $this->ownerSiteUserService->retrieve($merchantId);
        return $response;
    }

    /**
     * List the equipment available for a sales code.
     *
     * @param string|null $salesCode
     * @param string $equipmentSupplierCd
     * @param string $equipmentTypeCd
     * @param int $pageNumber
     * @param int $pageSize
     * @return EquipmentPageObject
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function listEquipmentCatalog(string $salesCode = null, string $equipmentSupplierCd, string $equipmentTypeCd, int $pageNumber, int $pageSize)
    {
        $response = $this->equipmentCatalogService->fetch($salesCode, $equipmentSupplierCd, $equipmentTypeCd, $pageNumber, $pageSize);
        return $response;
    }
}
