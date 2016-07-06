#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

$config = parse_ini_file('yougrabtube.ini');

$telegram = new Api($config['telegram_bot_key']);

$response = $telegram->getUpdates(
  ['offset' => -1, 'limit' => 1, 'timeout' => 0]);

$message = $response[0]->getMessage();
$chat = $message->getChat();

$response = $telegram->sendMessage([
  'chat_id' => $chat->getId(), 
  'text' => 'http://goo.gl/N8VEHY',
]);

$messageId = $response->getMessageId();


echo $messageId;