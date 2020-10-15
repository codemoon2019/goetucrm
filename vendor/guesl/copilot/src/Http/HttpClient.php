<?php

namespace Guesl\CardConnect\CoPilot\Http;

use GuzzleHttp\Client;

/**
 * Http Client
 *
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/2/14
 */
class HttpClient
{
    /**
     * Single instance.
     *
     * @var $instance
     */
    private static $instance;

    /**
     * GuzzleHttp
     *
     * @var Client $httpClient
     */
    protected $httpClient;

    /**
     * Base URI
     *
     * @var string
     */
    protected $baseUri;

    /**
     * HttpClient constructor.
     *
     * @param string|null $baseUri
     */
    protected function __construct($baseUri = null)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param mixed $instance
     */
    public static function setInstance($instance): void
    {
        self::$instance = $instance;
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
        if (isset($this->baseUri)) {
            $this->httpClient = new Client(["base_uri" => $this->baseUri]);
        } else {
            $this->httpClient = new Client();
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
            "headers" => [
                "Content-Type" => "application/json",
            ],
        ];
    }

    /**
     * Execute the http request by guzzleHttp.
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function execute(string $method, string $url = null, array $options = [])
    {
        $requestOptions = $this->getRequestOptions();

        if (!empty($options)) {
            $requestOptions = $options;
        }
        $response = $this->getHttpClient()->request($method, $url, $requestOptions);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    /**
     * Get request to the url.
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function get(string $url = null, array $options = [])
    {
        return $this->execute("GET", $url, $options);
    }

    /**
     * Post request the url.
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function post(string $url = null, array $options = [])
    {
        return $this->execute("POST", $url, $options);
    }

    /**
     * Post request the url.
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function put(string $url = null, array $options = [])
    {
        return $this->execute("PUT", $url, $options);
    }

    /**
     * Delete request.
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function delete(string $url = null, array $options = [])
    {
        return $this->execute("DELETE", $url, $options);
    }

    /**
     * Get http client instance.
     *
     * @param string $baseUrl
     * @return HttpClient
     */
    public static function getInstance(string $baseUrl = null)
    {
        if (static::$instance) {
            return static::$instance;
        } else {
            return new HttpClient($baseUrl);
        }
    }
}
