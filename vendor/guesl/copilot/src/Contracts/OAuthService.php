<?php

namespace Guesl\CardConnect\CoPilot\Contracts;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
interface OAuthService
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
}
