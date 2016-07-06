#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

// author : Moch Deden

$config = parse_ini_file('yougrabtube.ini');

$telegram = new Api($config['api_key']);

$response = $telegram->getUpdates(
  ['offset' => -1, 'limit' => 1, 'timeout' => 0]);

$message = $response[0]->getMessage();
$chat = $message->getChat();

$response = $telegram->sendMessage([
  'chat_id' => $chat->getId(), 
  'text' => $message->getText()
]);

$messageId = $response->getMessageId();

echo $messageId;