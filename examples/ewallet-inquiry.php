<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Ewallet;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$ewallet = new Ewallet($client);

$merchantTradeNo = '1234567890'; // same as in ewallet-create.php
$result = $ewallet->inquiry($merchantTradeNo);

echo $result->getBody()->getContents();
