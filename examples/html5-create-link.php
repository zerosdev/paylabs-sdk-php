<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Html5;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$h5 = new Html5($client);

$merchantTradeNo = '1234567890';

$result = $h5->create([
    'amount' => 10000,
    'merchantTradeNo' => $merchantTradeNo,
    'payer' => 'Customer Name',
    'phoneNumber' => 'Customer Phone',
    'goodsInfo' => 'Product Name',
    'notifyUrl' => 'https://yourwebsite.com/payment/notify',
    'redirectUrl' => 'https://yourwebsite.com/invoice/123',
    'lang' => 'id',
]);

echo $result->getBody()->getContents();
