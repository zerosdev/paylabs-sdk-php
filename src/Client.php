<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
use ZerosDev\Paylabs\Support\Constant;

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
        'request' => null,
        'response' => null,
    ];

    /**
     * Required configuration key
     *
     * @var array
     */
    private array $requiredConfigKeys = [
        'merchant_id',
        'api_key',
        'mode'
    ];

    /**
     * Reserved guzzle options that can't be overrided
     *
     * @var array
     */
    private array $reservedGuzzleOptions = [
        'base_uri',
        'on_stats',
    ];

    /**
     * Client instance
     *
     * You can use array config with the following keys
     * [
     *      'merchant_id' => '',
     *      'api_key' => '',
     *      'mode => '',
     *      'guzzle_options' => []
     * ]
     *
     * or use the positional arguments in the following sequence
     * $merchantId, $apiKey, $mode, $guzzleOptions
     *
     * @param array|string ...$args
     */
    public function __construct(...$args)
    {
        if (is_array($args[0])) {
            foreach ($this->requiredConfigKeys as $configKey) {
                if (!isset($args[0][$configKey])) {
                    throw new InvalidArgumentException("`{$configKey}` must be in the configuration value");
                }
            }
        } else {
            foreach ($this->requiredConfigKeys as $key => $configKey) {
                if (!isset($args[$key])) {
                    throw new InvalidArgumentException("`{$configKey}` must be in the configuration value");
                }
            }
        }

        $this->merchantId = (string) is_array($args[0]) ? $args[0]['merchant_id'] : $args[0];
        $this->apiKey = (string) is_array($args[0]) ? $args[0]['api_key'] : $args[1];
        $this->mode = (string) is_array($args[0]) ? $args[0]['mode'] : $args[2];

        $baseUri = ($this->mode == Constant::MODE_DEVELOPMENT)
            ? Constant::URL_DEVELOPMENT
            : Constant::URL_PRODUCTION;

        $options = [
            'base_uri' => $baseUri,
            'http_errors' => false,
            'connect_timeout' => 10,
            'timeout' => 30,
            'on_stats' => function (TransferStats $stats) {
                $hasResponse = $stats->hasResponse();
                $this->debugs = array_merge($this->debugs, [
                    'request' => [
                        'url' => (string) $stats->getEffectiveUri(),
                        'method' => $stats->getRequest()->getMethod(),
                        'headers' => (array) $stats->getRequest()->getHeaders(),
                        'body' => (string) $stats->getRequest()->getBody(),
                    ],
                    'response' => [
                        'status' => (int) ($hasResponse ? $stats->getResponse()->getStatusCode() : 0),
                        'headers' => (array) ($hasResponse ? $stats->getResponse()->getHeaders() : []),
                        'body' => (string) ($hasResponse ? $stats->getResponse()->getBody() : ""),
                    ],
                ]);
            }
        ];

        $guzzleOptions = (array) is_array($args[0]) ? ($args[0]['guzzle_options'] ?? []) : ($args[3] ?? []);

        foreach ($this->reservedGuzzleOptions as $reserved) {
            unset($guzzleOptions[$reserved]);
        }

        $options = array_merge($options, $guzzleOptions);

        parent::__construct($options);
    }

    public function debugs()
    {
        return (object) $this->debugs;
    }
}
