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
                $payloads[$k] = self::sortPayload($payloads[$k]);
            } elseif (is_string($v) && substr($v, 0, 2) == '{"' && substr($v, -2) == '"}') { // detected as JSON
                $p = json_decode($v, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }
                $payloads[$k] = json_encode(self::sortPayload($p));
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
     * @return string
     */
    public static function formatAmount($amount): string
    {
        if (!is_numeric($amount)) {
            throw new Exception('Amount must be numeric value');
        }

        return number_format($amount, 2, '.', '');
    }
}
