<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Psr7\Response;
use ZerosDev\Paylabs\Support\Helper;

class Ewallet
{
    /**
     * Client instance
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Initialize Ewallet class
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create ewallet
     *
     * @param array $payloads
     * @return \GuzzleHttp\Psr7\Response
     */
    public function create(array $payloads): Response
    {
        if (!isset($payloads['requestId'])) {
            $payloads['requestId'] = Helper::createRequestId();
        }

        // No need to enter signature manually, we've added it
        if (isset($payloads['sign'])) {
            unset($payloads['sign']);
        }

        $payloads['amount'] = Helper::formatAmount($payloads['amount']);
        $payloads['merchantId'] = $this->client->merchantId;
        $this->client->debugs['str_to_sign'] = Helper::createStrToSign($payloads, $this->client->apiKey);
        $payloads['sign'] = Helper::createSignature($this->client->debugs['str_to_sign'], $this->client->apiKey);

        return $this->client->post('ewallet/create', [
            'json' => $payloads
        ]);
    }

    /**
     * Inquiry Ewallet
     *
     * @param string $merchantTradeNo
     * @return \GuzzleHttp\Psr7\Response
     */
    public function inquiry(string $merchantTradeNo): Response
    {
        $payloads = [
            'requestId' => Helper::createRequestId(),
            'merchantId' => $this->client->merchantId,
            'merchantTradeNo' => $merchantTradeNo,
        ];
        $this->client->debugs['str_to_sign'] = Helper::createStrToSign($payloads, $this->client->apiKey);
        $payloads['sign'] = Helper::createSignature($this->client->debugs['str_to_sign'], $this->client->apiKey);

        return $this->client->post('ewallet/query', [
            'json' => $payloads
        ]);
    }
}
