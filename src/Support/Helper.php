<?php

namespace ZerosDev\Paylabs\Support;

use Exception;
use Ramsey\Uuid\Uuid;

class Helper
{
    /**
     * Sort ascending payloads by the keys
     *
     * @param array $payloads
     * @return array
     */
    public static function sortPayload(array $payloads): array
    {
        // Sort ascending by key name
        ksort($payloads);

        foreach ($payloads as $k => $v) {
            if (is_array($v)) {
                ksort($payloads[$k]);
            }
        }

        return $payloads;
    }

    /**
     * Create signature from payloads
     *
     * @param array $payloads
     * @param string $apiKey
     * @return string
     */
    public static function createSignature(array $payloads, string $apiKey): string
    {
        $payloads = array_filter($payloads, function ($v) {
            return !empty($v);
        });

        $payloads = self::sortPayload($payloads);
        $strToSign = [];
        foreach ($payloads as $k => $v) {
            $strToSign[] = $k . '=' . $v;
        }
        $strToSign[] = 'key=' . $apiKey;

        $signature = implode('&', $strToSign);
        $signature = hash('sha256', $signature);

        return $signature;
    }

    /**
     * Create unique request id
     *
     * @return string
     */
    public static function createRequestId(): string
    {
        return Uuid::uuid4();
    }

    /**
     * Format amount to decimal number with two decimal point
     *
     * @param mixed $amount
     * @return float
     */
    public static function formatAmount($amount)
    {
        if (!is_numeric($amount)) {
            throw new Exception('Amount must be numeric value');
        }

        return number_format($amount, 2, '.', '');
    }
}
