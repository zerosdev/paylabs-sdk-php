<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\VirtualAccount;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$va = new VirtualAccount($client);

$merchantTradeNo = '1234567890';

$result = $va->create([
    'paymentType' => 'SinarmasVA',
    'amount' => 10000,
    'merchantTradeNo' => $merchantTradeNo,
    'notifyUrl' => 'https://yourwebsite.com/payment/notify',
    'payer' => 'Customer Name',
    'goodsInfo' => 'Product Name'
]);

echo $result->getBody()->getContents();
