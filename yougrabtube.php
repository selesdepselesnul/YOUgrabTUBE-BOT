#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

$response = Unirest\Request::get("https://ytgrabber.p.mashape.com/app/get/vbb_DKn21Ig",
  array(
    "X-Mashape-Key" => "S60LBMB0ivmshGLcOVyPhT6KTFITp1jjiszjsnQpNmujBNVPuS",
    "Accept" => "application/json"
  )
);
$links = $response->body->link;


// $config = parse_ini_file('yougrabtube.ini');

// $telegram = new Api($config['telegram_bot_key']);

// $response = $telegram->getUpdates(
//   ['offset' => -1, 'limit' => 1, 'timeout' => 0]);

// $message = $response[0]->getMessage();
// $chat = $message->getChat();

// $response = $telegram->sendMessage([
//   'chat_id' => $chat->getId(), 
//   'text' => 'http://goo.gl/N8VEHY',
// ]);

// $messageId = $response->getMessageId();


// echo $messageId;