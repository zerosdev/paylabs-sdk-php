<?php

namespace ZerosDev\Paylabs\Support;

use Exception;
use Ramsey\Uuid\Uuid;
use UnexpectedValueException;

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
                $payloads[$k] = json_encode(self::sortPayload($p), JSON_UNESCAPED_SLASHES);
            }
        }

        return $payloads;
    }

    /**
     * Create string to be signed
     *
     * @param array $payloads
     * @param string $apiKey
     * @return string
     */
    public static function createStrToSign(array $payloads, string $apiKey): string
    {
        $payloads = array_filter($payloads, function ($v) {
            return !empty($v);
        });

        $payloads = self::sortPayload($payloads);
        $strToSign = [];
        foreach ($payloads as $k => $v) {
            if (is_array($v)) {
                $strToSign[] = $k . '=' . json_encode($v, JSON_UNESCAPED_SLASHES);
            } else {
                $strToSign[] = $k . '=' . $v;
            }
        }
        $strToSign[] = 'key=' . $apiKey;

        return implode('&', $strToSign);
    }

    /**
     * Create signature from payloads
     *
     * @param array|string $payloads
     * @param string $apiKey
     * @return string
     */
    public static function createSignature($payloads, string $apiKey): string
    {
        $signature = is_array($payloads) ? self::createStrToSign($payloads, $apiKey) : $payloads;
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
     *
     * @throws \UnexpectedValueException
     */
    public static function formatAmount($amount): string
    {
        if (!is_numeric($amount)) {
            throw new UnexpectedValueException('Amount must be numeric value');
        }

        return number_format($amount, 2, '.', '');
    }
}
