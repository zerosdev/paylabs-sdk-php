<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use ZerosDev\Paylabs\Support\Constant;
use ZerosDev\Paylabs\Support\Helper;

class Client extends HttpClient
{
    /**
     * Merchant id
     *
     * @var string|null
     */
    public ?string $merchantId;

    /**
     * API Key
     *
     * @var string|null
     */
    public ?string $apiKey;

    /**
     * API mode
     *
     * @var string|null
     */
    public ?string $mode;

    /**
     * Request payloads
     *
     * @var array
     */
    public array $payloads = [];

    /**
     * Debugs payload
     *
     * @var array
     */
    public array $debugs = [
        'str_to_sign' => null,
        'request' => null,
        'response' => null,
    ];

    /**
     * Initialize Client
     *
     * @param string $merchantId
     * @param string $apiKey
     * @param string $mode
     * @param array $guzzleOptions
     */
    public function __construct(string $merchantId, string $apiKey, string $mode = Constant::MODE_DEVELOPMENT, array $guzzleOptions = [])
    {
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
        $this->mode = $mode;

        $baseUri = $this->mode == Constant::MODE_DEVELOPMENT
            ? Constant::URL_DEVELOPMENT
            : Constant::URL_PRODUCTION;

        $options = [
            'base_uri' => $baseUri,
            'http_erros' => false,
            'connect_timeout' => 10,
            'timeout' => 30,
            'on_stats' => function (TransferStats $stats) {
                $this->debugs = array_merge($this->debugs, [
                    'request' => [
                        'url' => (string) $stats->getEffectiveUri(),
                        'method' => $stats->getRequest()->getMethod(),
                        'headers' => (array) $stats->getRequest()->getHeaders(),
                        'body' => (string) $stats->getRequest()->getBody(),
                    ],
                    'response' => [
                        'status' => (int) $stats->getResponse()->getStatusCode(),
                        'headers' => (array) $stats->getResponse()->getHeaders(),
                        'body' => (string) $stats->getResponse()->getBody(),
                    ],
                ]);
            }
        ];

        // `on_stats` can't be override
        unset($guzzleOptions['on_stats']);

        $options = array_merge($options, $guzzleOptions);

        parent::__construct($options);
    }

    public function debugs()
    {
        return (object) $this->debugs;
    }
}
