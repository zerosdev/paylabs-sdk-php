<?php

require '../vendor/autoload.php';

use ZerosDev\Paylabs\Client as PaylabsClient;
use ZerosDev\Paylabs\Notification;

$config = require __DIR__ . '/config.php';

$client = new PaylabsClient($config['merchant_id'], $config['api_key'], $config['mode'], $config['guzzle_options']);
$notificationJsonData = file_get_contents("php://input");
$notification = new Notification($client, $notificationJsonData);

// Notification signature is not valid
if (!$notification->valid()) {
    echo 'Signature verification failed';
    die;
}

$data = $notification->data();

print_r($data);
