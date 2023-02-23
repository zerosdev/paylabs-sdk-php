<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
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

        $merchantId = (string) is_array($args[0]) ? $args[0]['merchant_id'] : $args[0];
        $apiKey = (string) is_array($args[0]) ? $args[0]['api_key'] : $args[1];
        $mode = (string) is_array($args[0]) ? $args[0]['mode'] : $args[2];
        $guzzleOptions = (array) is_array($args[0]) ? $args[0]['guzzle_options'] ?? [] : $args[3] ?? [];

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

        // `on_stats` can't be overrided
        unset($guzzleOptions['on_stats']);

        $options = array_merge($options, $guzzleOptions);

        parent::__construct($options);
    }

    public function debugs()
    {
        return (object) $this->debugs;
    }
}
