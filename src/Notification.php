<?php

namespace ZerosDev\Paylabs;

use ZerosDev\Paylabs\Support\Helper;

class Notification
{
    /**
     * Client instance
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Notification JSON payload
     *
     * @var string
     */
    protected ?string $json;

    /**
     * Initialize Qris class
     *
     * @param Client $client
     * @param ?string $json
     */
    public function __construct(Client $client, ?string $json = null)
    {
        $this->client = $client;
        $this->json = $json;
    }

    /**
     * Verify is notification signature is valid or not
     *
     * @param ?string $json
     * @return boolean
     */
    public function valid(?string $json = null): bool
    {
        if ($json) {
            $this->json = $json;
        }

        if (empty($this->json) && function_exists('file_get_contents')) {
            $this->json = file_get_contents("php://input") ?? null;
        }

        $payloads = json_decode($this->json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (isset($payloads['amount'])) {
            $payloads['amount'] = Helper::formatAmount($payloads['amount']);
        }

        $incomingSignature = $payloads['sign'] ?? '';

        // Delete sign from payloads because payloads will be used to generate local signature
        unset($payloads['sign']);

        $strToSign = Helper::createStrToSign($payloads, $this->client->apiKey);
        $this->client->debugs['str_to_sign'] = $strToSign;
        $localSignature = Helper::createSignature($strToSign, $this->client->apiKey);
        $this->client->debugs['local_signature'] = $localSignature;

        return hash_equals($localSignature, $incomingSignature);
    }

    /**
     * Parse notification payload
     *
     * @return object|null
     */
    public function data(): ?object
    {
        return json_decode($this->json);
    }
}
