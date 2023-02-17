<?php

namespace ZerosDev\Paylabs\Support;

class Helper
{
    public static function makeSignature(array $payloads, string $apiKey)
    {
        // Sort ascending by key name
        ksort($payloads);

        $payloads['key'] = $apiKey;

        $signature = http_build_query($payloads);
        $signature = hash('sha256', $signature);

        return $signature;
    }
}
