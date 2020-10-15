<?php

namespace Guesl\CardConnect\CoPilot\Services;

use Guesl\CardConnect\CoPilot\Contracts\OAuthService;
use Guesl\CardConnect\CoPilot\Exceptions\CoPilotException;
use Guesl\CardConnect\CoPilot\Http\HttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

/**
 * Created by Jianfeng Li.
 * User => Jianfeng Li
 * Date => 2018/05/09
 */
class OAuthServiceImpl implements OAuthService
{
    const ACCESS_TOKEN_URL_UAT = "https://accountsuat.cardconnect.com/auth/realms/cardpointe/protocol/openid-connect/token";
    const ACCESS_TOKEN_URL = "https://accountsuat.cardconnect.com/auth/realms/cardpointe/protocol/openid-connect/token";

    /**
     * CoPilot access token url.
     *
     * @var string $accessTokenUrl
     */
    private $accessTokenUrl;

    /**
     * CoPilot test mode.
     *
     * @var bool $testMode
     */
    private $testMode;

    /**
     * CoPilot API username.
     *
     * @var string username
     */
    private $username;

    /**
     * CoPilot API password.
     *
     * @var string password
     */
    private $password;

    /**
     * CoPilot client secret.
     *
     * @var string client secret
     */
    private $clientSecret;

    /**
     * CoPilot http client.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * StripeProvider constructor.
     *
     * @param $config
     * @var array
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
            $this->accessTokenUrl = static::ACCESS_TOKEN_URL_UAT;
        } else {
            $this->accessTokenUrl = static::ACCESS_TOKEN_URL;
        }

        // Get the http client
        $this->httpClient = HttpClient::getInstance($this->accessTokenUrl);
    }

    /**
     * Create access token.
     *
     * @return mixed
     * @throws CoPilotException
     */
    public function createAccessToken()
    {
        $options = [
            "headers" => [
                "Content-Type" => "application/x-www-form-urlencoded",
            ],

            "form_params" => [
                "username" => $this->username,
                "password" => $this->password,
                "grant_type" => "password",
                "client_id" => "copilot",
                "client_secret" => $this->clientSecret,
            ],
        ];

        try {
            $response = $this->httpClient->post("", $options);
            Cache::forever("copilot_password_token", $response);

            return $response;
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Refresh access token.
     *
     * @param $refreshToken
     * @return mixed
     * @throws CoPilotException
     */
    public function refreshAccessToken($refreshToken)
    {
        $options = [
            "headers" => [
                "Content-Type" => "application/x-www-form-urlencoded",
            ],

            "form_params" => [
                "grant_type" => "refresh_token",
                "client_id" => "copilot",
                "refresh_token" => $refreshToken,
                "client_secret" => $this->clientSecret,
            ],
        ];

        try {
            $response = $this->httpClient->post("", $options);
            Cache::forever("copilot_password_token", $response);

            return $response;
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
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
        $errorJson = (string)$response->getBody();

        $error = json_decode($errorJson, true);

        throw new CoPilotException($error["error"], $error["error_description"], null, $exception->getResponse()->getStatusCode());
    }
}
