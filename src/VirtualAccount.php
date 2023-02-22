<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Psr7\Response;
use ZerosDev\Paylabs\Support\Helper;

class VirtualAccount
{
    /**
     * Client instance
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Initialize Virtual Account class
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create virtual account
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
        $payloads['sign'] = Helper::createSignature($payloads, $this->client->apiKey);

        return $this->client->post('va/create', [
            'json' => $payloads
        ]);
    }

    /**
     * Inquiry Virtual Account
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
        $payloads['sign'] = Helper::createSignature($payloads, $this->client->apiKey);

        return $this->client->post('va/query', [
            'json' => $payloads
        ]);
    }
}
