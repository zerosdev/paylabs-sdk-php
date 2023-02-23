<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Qris;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode'], $config['guzzle_options']);
$qris = new Qris($client);

$merchantTradeNo = '1234567890';

$result = $qris->create([
    'paymentType' => 'QRIS',
    'amount' => 10000,
    'merchantTradeNo' => $merchantTradeNo,
    'notifyUrl' => 'https://yourwebsite.com/payment/notify',
    'goodsInfo' => 'Product Name'
]);

echo $result->getBody()->getContents();
