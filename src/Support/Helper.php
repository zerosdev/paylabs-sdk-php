<?php

namespace ZerosDev\Paylabs\Support;

trait Helper
{
    public function makeSignature()
    {
        $payloads = $this->payloads;

        // Sort ascending by key name
        ksort($payloads);

        $payloads['key'] = $this->apiKey;

        $signature = http_build_query($payloads);
        $signature = hash('sha256', $signature);

        return $signature;
    }
}
