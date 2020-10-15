<?php

namespace Guesl\CardConnect\CoPilot\Services;

use Guesl\CardConnect\CoPilot\Contracts\AbstractBaseService;
use Guesl\CardConnect\CoPilot\Contracts\MerchantService;
use Guesl\CardConnect\CoPilot\Models\Attachment;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\MerchantStatus;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\SignatureStatus;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Created by Jianfeng Li.
 * User => Jianfeng Li
 * Date => 2018/04/13
 */
class MerchantServiceImpl extends AbstractBaseService implements MerchantService
{
    /**
     * CoPilot Merchant sale code.
     *
     * @var string
     */
    protected $salesCode;

    private $url = "/merchant";

    /**
     * Create Merchant.
     *
     * @param int $templateId
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function create(int $templateId, Merchant $merchant, OwnerSiteUser $ownerUser = null)
    {
        Log::debug(get_class($this) . "::create => Create Merchant.");

        $merchantSalesCode = $merchant->getSalesCode();
        if (empty($merchantSalesCode)) {
            $merchant->setSalesCode($this->salesCode);
        }

        $options = $this->getRequestHeader();
        $parameters = [
            "templateId" => $templateId,
            "merchant" => $merchant,
            "ownerSiteUser" => $ownerUser,
        ];
        $options["json"] = $parameters;

        try {
            $response = $this->httpClient->post($this->url, $options);
            return $response;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Update merchant.
     *
     * @param int $id
     * @param Merchant $merchant
     * @param OwnerSiteUser|null $ownerUser
     * @return mixed
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function update(int $id, Merchant $merchant, OwnerSiteUser $ownerUser = null)
    {
        Log::debug(get_class($this) . "::update => Update Merchant.");

        $merchantSalesCode = $merchant->getSalesCode();
        if (empty($merchantSalesCode)) {
            $merchant->setSalesCode($this->salesCode);
        }

        $options = $this->getRequestHeader();
        $parameters = [
            "merchant" => $merchant,
            "ownerSiteUser" => $ownerUser,
        ];
        $options["json"] = $parameters;

        try {
            $response = $this->httpClient->put($this->url . "/$id", $options);
            return $response;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Retrieve merchant.
     *
     * @param int $id
     * @return Merchant
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function retrieve(int $id)
    {
        Log::debug(get_class($this) . "::retrieve => Retrieve merchant.");

        $options = $this->getRequestHeader();

        try {
            $response = $this->httpClient->get($this->url . "/$id", $options);
            $merchant = (new Merchant())->populate($response["merchant"]);
            return $merchant;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Retrieve merchant status.
     *
     * @param int $id
     * @return MerchantStatus
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function retrieveStatus(int $id)
    {
        Log::debug(get_class($this) . "::retrieveStatus => Retrieve merchant status.");

        $options = $this->getRequestHeader();

        try {
            $response = $this->httpClient->get($this->url . "/$id/status", $options);
            $merchantStatus = (new MerchantStatus())->populate($response["merchantStatus"]);
            return $merchantStatus;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Request Merchant Signature.
     *
     * If the request signature was successful, the signature url will be returned.
     *
     * @param int $id
     * @return string
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function sign(int $id)
    {
        Log::debug(get_class($this) . "::sign => Request Merchant Signature.");

        $options = $this->getRequestHeader();

        try {
            $response = $this->httpClient->put($this->url . "/$id/signature", $options);
            $signatureUrl = $response["signatureUrl"];
            return $signatureUrl;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Request merchant signature status.
     *
     * @param int $id
     * @return SignatureStatus
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function retrieveSignatureStatus($id)
    {
        Log::debug(get_class($this) . "::retrieveSignatureStatus => Request merchant signature status.");

        $options = $this->getRequestHeader();

        try {
            $response = $this->httpClient->get($this->url . "/$id/signature", $options);
            $signatureStatus = (new SignatureStatus())->populate($response["signatureStatus"]);
            return $signatureStatus;

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Put the merchant back in the In Progress state.
     *
     * @param $id
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function retractSignature($id)
    {
        Log::debug(get_class($this) . "::retractSignature => Put the merchant back in the In Progress state.");

        $options = $this->getRequestHeader();

        try {
            $this->httpClient->delete($this->url . "/$id/signature", $options);

        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Create a merchant attachment.
     *
     * return attachment id.
     *
     * @param $id
     * @param Attachment $attachment
     * @return int
     * @throws \Guesl\CardConnect\CoPilot\Exceptions\CoPilotException
     */
    public function createAttachment($id, Attachment $attachment)
    {
        Log::debug(get_class($this) . "::retractSignature => Put the merchant back in the In Progress state.");

        $options = $this->getRequestHeader();
        $parameters = [
            "attachment" => $attachment,
        ];
        $options["json"] = $parameters;

        try {
            $response = $this->httpClient->put($this->url . "/$id/attachment", $options);
            return $response["attachmentId"];
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }
}
