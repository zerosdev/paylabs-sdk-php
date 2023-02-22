<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\CreditCard;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode']);
$cc = new CreditCard($client);

$merchantTradeNo = '1234567890'; // same as in credit-card-create.php
$result = $cc->inquiry($merchantTradeNo);

echo $result->getBody()->getContents();
