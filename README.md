<h1 align="center">paylabs-sdk-php</h1>
<h6 align="center">Unofficial Paylabs.co.id Integration Kit for PHP</h6>

<p align="center">
  <img src="https://img.shields.io/github/v/release/zerosdev/paylabs-sdk-php?include_prereleases" alt="release"/>
  <img src="https://img.shields.io/github/languages/top/zerosdev/paylabs-sdk-php" alt="language"/>
  <img src="https://img.shields.io/github/license/zerosdev/paylabs-sdk-php" alt="license"/>
  <img src="https://img.shields.io/github/languages/code-size/zerosdev/paylabs-sdk-php" alt="size"/>
  <img src="https://img.shields.io/github/downloads/zerosdev/paylabs-sdk-php/total" alt="downloads"/>
  <img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg" alt="pulls"/>
</p>

## Requirements
- PHP v7.2+
- PHP JSON Extension

## Installation

1. Run command
```
composer require zerosdev/paylabs-sdk-php
```

## Usage

### Notes
- The following payloads have been added automatically so you don't have to enter them manually
  - requestId (* You can still enter your own requestId, otherwise we will generate random UUIDv4 for the requestId)
  - merchantId
  - sign

```php
<?php

require 'path/to/your/vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Support\Constant;
use ZerosDev\Paylabs\VirtualAccount;

$merchantId = '12345';
$apiKey = 'd1cfd***********888ed3';
$mode = Constant::MODE_DEVELOPMENT;
$guzzleOptions = []; // Your additional Guzzle options (https://docs.guzzlephp.org/en/stable/request-options.html)

$client = new PaylabsClient($merchantId, $apiKey, $mode, $guzzleOptions);
$va = new VirtualAccount($client);

$result = $va->create([
    'paymentType' => 'SinarmasVA',
    'amount' => 10000,
    'merchantTradeNo' => uniqid(),
    'notifyUrl' => 'https://yourwebsite.com/payment/notify',
    'payer' => 'Customer Name',
    'goodsInfo' => 'Product Name'
]);

$debugs = $client->debugs();
echo json_encode($debugs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```

Please check the `/examples` for the other examples
