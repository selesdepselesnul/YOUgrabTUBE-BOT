#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

$config = parse_ini_file('yougrabtube.ini');

$telegram = new Api($config['api_key']);

$response = $telegram->getUpdates(
  ['offset' => -1, 'limit' => 1, 'timeout' => 0]);
$update = $response[0];
print_r($response[0]->getMessage());