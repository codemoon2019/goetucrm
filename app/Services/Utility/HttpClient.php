<?php

namespace App\Services\Utility;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * It is a http client call from php, only offer get and post method for now.
 *
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/2/14
 * Time: 16:00
 */
class HttpClient
{

    private static $instance;

    /**
     * GuzzleHttp
     *
     * @var
     */
    protected $httpClient;

    protected $baseUri;

    protected $newBaseUrl;

    /**
     * HttpClient constructor.
     * @param string|null $baseUri
     */
    protected function __construct($baseUri = null)
    {
        if (!$this->baseUri) {
            $this->baseUri = $baseUri;
        }
        $this->newBaseUrl = $baseUri;
    }

    private function __clone()
    {
    }

    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return Client
     */
    protected function getHttpClient()
    {
        if ($this->baseUri != $this->newBaseUrl || is_null($this->httpClient)) {
            $this->httpClient = new Client(["base_uri" => $this->newBaseUrl]);
        }

        return $this->httpClient;
    }

    /**
     * Get the default options for an HTTP request.
     *
     * @return array
     */
    protected function getRequestOptions()
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
            ],
        ];
    }

    /**
     * Execute the http request by guzzleHttp.
     *
     * @param string $method
     * @param string $url
     * @param array $paramArray
     * @return mixed
     */
    public function execute($method, $url, $paramArray = [])
    {
        try {
            $requestOptions = $this->getRequestOptions();

            if (!empty($paramArray)) {
                $requestOptions["query"] = $paramArray;
            }
            $response = $this->getHttpClient()->request($method, $url, $requestOptions);

            $statusCode = $response->getStatusCode();
            if (200 == $statusCode) {
                Log::debug(get_class($this) . "::execute , request to remote server and response 200.");

            } else if (500 == $statusCode) {
                Log::error(get_class($this) . "::execute , request event to remote server but response 500.");
            }
            $result = json_decode($response->getBody(), true);

            return $result;
        } catch (Exception $e) {
            Log::error(get_class($this) . "::execute \n" . $e->getMessage());
        }
    }

    /**
     * Get request to the url.
     *
     * @param $url
     * @param array $paramArray
     * @return mixed
     */
    public function get($url, $paramArray = [])
    {
        return $this->execute("GET", $url, $paramArray);
    }

    /**
     * Post request the url.
     *
     * @param $url
     * @param array $paramArray
     * @return mixed
     */
    public function post($url, $paramArray = [])
    {
        return $this->execute("POST", $url, $paramArray);
    }

    public static function getInstance($baseUrl = null)
    {
        if (static::$instance) {
            return static::$instance;
        } else {
            return new HttpClient($baseUrl);
        }
    }

}