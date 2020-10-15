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
interface CoPilotService
{
    /**
     * Create access token.
     *
     * @return mixed
     */
    public function createAccessToken();

    /**
     * Refresh access token.
     *
     * @param $refreshToken
     * @return mixed
     */
    public function refreshAccessToken($refreshToken);

    /**
     * Create Merchant.
     *
     * @param int $templateId
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return int
     */
    public function createMerchant(int $templateId, Merchant $merchant, OwnerSiteUser $ownerUser = null);

    /**
     * Update merchant.
     *
     * @param int $id
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function updateMerchant(int $id, Merchant $merchant, OwnerSiteUser $ownerUser = null);

    /**
     * Retrieve merchant.
     *
     * @param int $id
     * @return Merchant
     */
    public function retrieveMerchant(int $id);

    /**
     * Retrieve merchant status.
     *
     * @param int $id
     * @return mixed
     */
    public function retrieveMerchantStatus(int $id);

    /**
     * Request Merchant Signature.
     *
     * If the request signature was successful, the signature url will be returned.
     *
     * @param int $id
     * @return string
     */
    public function signMerchant(int $id);

    /**
     * Request merchant signature status.
     *
     * @param int $id
     * @return SignatureStatus
     */
    public function retrieveMerchantSignatureStatus($id);

    /**
     * Put the merchant back in the In Progress state.
     *
     * @param $id
     */
    public function retractMerchantSignature($id);

    /**
     * Create a merchant attachment.
     *
     * return attachment id.
     *
     * @param $id
     * @param Attachment $attachment
     * @return int
     */
    public function createMerchantAttachment($id, Attachment $attachment);

    /**
     * Create owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser $ownerUser
     * @return mixed
     */
    public function createOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser);

    /**
     * Update owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function updateOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser = null);

    /**
     * Retrieve owner site user of the merchant.
     *
     * @param int $merchantId
     * @return OwnerSiteUser
     */
    public function retrieveOwnerSiteUser(int $merchantId);

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
    public function listEquipmentCatalog(string $salesCode = null, string $equipmentSupplierCd, string $equipmentTypeCd, int $pageNumber, int $pageSize);
}
