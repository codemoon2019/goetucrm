<?php

namespace Guesl\CardConnect\CoPilot\Services;

use Guesl\CardConnect\CoPilot\Contracts\AbstractBaseService;
use Guesl\CardConnect\CoPilot\Contracts\OwnerSiteUserService;
use Guesl\CardConnect\CoPilot\Models\Owner;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Created by Jianfeng Li.
 * User => Jianfeng Li
 * Date => 2018/04/13
 */
class OwnerSiteUserServiceImpl extends AbstractBaseService implements OwnerSiteUserService
{
    /**
     * CoPilot Merchant sale code.
     *
     * @var string
     */
    protected $salesCode;

    private $url = "/merchant";

    /**
     * Create owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser $ownerUser
     * @return mixed
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function create(int $merchantId, OwnerSiteUser $ownerUser)
    {
        Log::debug(get_class($this) . "::create => Create owner site user for \"CardPoint\".");

        $options = $this->getRequestHeader();
        $parameters = [
            "ownerSiteUser" => $ownerUser,
        ];
        $options["json"] = $parameters;

        try {
            $response = $this->httpClient->put($this->url . "/$merchantId/ownerSiteUser", $options);
            return $response;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Update owner site user for "CardPoint".
     *
     * @param int $merchantId
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function update(int $merchantId, OwnerSiteUser $ownerUser = null)
    {
        Log::debug(get_class($this) . "::update => Create owner site user for \"CardPoint\".");

        $options = $this->getRequestHeader();
        $parameters = [
            "ownerSiteUser" => $ownerUser,
        ];
        $options["json"] = $parameters;

        try {
            $response = $this->httpClient->put($this->url . "/$merchantId/ownerSiteUser", $options);
            return $response;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Retrieve owner site user of the merchant.
     *
     * @param int $merchantId
     * @return OwnerSiteUser
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function retrieve(int $merchantId)
    {
        $options = $this->getRequestHeader();

        try {
            $response = $this->httpClient->get($this->url . "/$merchantId/ownerSiteUser", $options);
            $ownerSiteUser = (new OwnerSiteUser())->populate($response["ownerSiteUser"]);
            return $ownerSiteUser;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }
}
