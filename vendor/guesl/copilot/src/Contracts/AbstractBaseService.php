<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use Guesl\CardConnect\CoPilot\Exceptions\CoPilotException;
use Guesl\CardConnect\CoPilot\Http\HttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
abstract class AbstractBaseService
{
    const COPILOT_API_URL = "https://copilot-api-uat.cardconnect.com/";
    const COPILOT_API_URL_UAT = "https://copilot-api-uat.cardconnect.com/";

    /**
     * CoPilot API base url.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * CoPilot test mode.
     *
     * @var bool $testMode
     */
    protected $testMode;

    /**
     * CoPilot http client.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * CoPilot version.
     *
     * @var string
     */
    protected $version = "1.0";

    /**
     * StripeProvider constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        // Get the access url
        if ($this->testMode) {
            $this->baseUrl = static::COPILOT_API_URL_UAT;
        } else {
            $this->baseUrl = static::COPILOT_API_URL;
        }

        $this->httpClient = HttpClient::getInstance($this->baseUrl);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * @param bool $testMode
     */
    public function setTestMode(bool $testMode): void
    {
        $this->testMode = $testMode;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * Get the common request header options.
     *
     * @return array
     */
    protected function getRequestHeader()
    {
        $passwordToken = Cache::get("copilot_password_token");
        $options = [
            "headers" => [
                "Authorization" => "Bearer " . $passwordToken["access_token"],
                "Content-Type" => "application/json",
                "X-CopilotAPI-Version" => $this->getVersion(),
            ],
        ];

        return $options;
    }

    /**
     * Handle the exception thrown from request.
     *
     * @param RequestException $exception
     * @throws CoPilotException
     */
    protected function handleException(RequestException $exception)
    {
        $response = $exception->getResponse();
        $errorsJson = (string)$response->getBody();

        $errors = json_decode($errorsJson, true);

        if (isset($errors) && array_key_exists("errors", $errors)) {
            $error = $errors["errors"][0];
            throw new CoPilotException($error["code"], $error["message"], null, $exception->getResponse()->getStatusCode());
        } else {
            throw new CoPilotException("unknown", $exception->getMessage(), null, $exception->getResponse()->getStatusCode());
        }
    }
}
