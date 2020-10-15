<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
interface OwnerSiteUserService
{
    /**
     * Create owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser $ownerUser
     * @return mixed
     */
    public function create(int $merchantId, OwnerSiteUser $ownerUser);

    /**
     * Update owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     */
    public function update(int $merchantId, OwnerSiteUser $ownerUser = null);

    /**
     * Retrieve owner site user of the merchant.
     *
     * @param int $merchantId
     * @return OwnerSiteUser
     */
    public function retrieve(int $merchantId);
}
