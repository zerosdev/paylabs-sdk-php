<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Qris;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$qris = new Qris($client);

$merchantTradeNo = '1234567890'; // same as in qris-create.php
$result = $qris->inquiry($merchantTradeNo);

echo $result->getBody()->getContents();
