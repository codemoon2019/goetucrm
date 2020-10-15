<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use Guesl\CardConnect\CoPilot\Models\Attachment;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\MerchantStatus;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\SignatureStatus;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
interface MerchantService
{
    /**
     * Create Merchant.
     *
     * @param int $templateId
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function create(int $templateId, Merchant $merchant, OwnerSiteUser $ownerUser = null);

    /**
     * Update merchant.
     *
     * @param int $id
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function update(int $id, Merchant $merchant, OwnerSiteUser $ownerUser = null);

    /**
     * Retrieve merchant.
     *
     * @param int $id
     * @return Merchant
     */
    public function retrieve(int $id);

    /**
     * Retrieve merchant status.
     *
     * @param int $id
     * @return MerchantStatus
     */
    public function retrieveStatus(int $id);

    /**
     * Request merchant signature.
     *
     * If the request signature was successful, the signature url will be returned.
     *
     * @param int $id
     * @return string
     */
    public function sign(int $id);

    /**
     * Request merchant signature status.
     *
     * @param int $id
     * @return SignatureStatus
     */
    public function retrieveSignatureStatus($id);

    /**
     * Put the merchant back in the In Progress state.
     *
     * @param $id
     */
    public function retractSignature($id);

    /**
     * Create a merchant attachment.
     *
     * return attachment id.
     *
     * @param $id
     * @param Attachment $attachment
     * @return int
     */
    public function createAttachment($id, Attachment $attachment);
}
