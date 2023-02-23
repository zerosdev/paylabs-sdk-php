<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\VirtualAccount;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode'], $config['guzzle_options']);
$va = new VirtualAccount($client);

$merchantTradeNo = '1234567890'; // same as in virtual-account-create.php
$result = $va->inquiry($merchantTradeNo);

echo $result->getBody()->getContents();
