<?php

namespace ZerosDev\Paylabs;

use GuzzleHttp\Client as HttpClient;
use ZerosDev\Paylabs\Support\Helper;

class Client
{
    use Helper;

    protected $merchantId;
    protected $apiKey;

    public function __construct(string $merchantId, string $apiKey)
    {
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
    }
}
