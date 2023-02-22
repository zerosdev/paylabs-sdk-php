<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\CreditCard;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$cc = new CreditCard($client);

$merchantTradeNo = '1234567890';

$result = $cc->create([
    'paymentType' => 'CreditCard',
    'amount' => 10000,
    'merchantTradeNo' => $merchantTradeNo,
    'notifyUrl' => 'https://yourwebsite.com/payment/notify',
    'paymentParams' => [
        'redirectUrl' => 'https://yourwebsite.com/invoice/123',
    ],
    'goodsInfo' => 'Product Name'
]);

echo $result->getBody()->getContents();
