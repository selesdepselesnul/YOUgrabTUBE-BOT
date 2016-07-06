#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

$config = parse_ini_file('yougrabtube.ini');
$response = Unirest\Request::get("https://ytgrabber.p.mashape.com/app/get/vbb_DKn21Ig",
  [
    "X-Mashape-Key" => $config['mashape_key'],
    "Accept" => "application/json"
  ]
);

$links = $response->body->link;
$telegram = new Api($config['telegram_bot_key']);

$response = $telegram->getUpdates(
  ['offset' => -1, 'limit' => 1, 'timeout' => 0]);

$message = $response[0]->getMessage();
$chat = $message->getChat();

foreach ($links as $link) {
    
    $response = Unirest\Request::get(
      "https://sjehutch-passbeemedia-shorturl.p.mashape.com/CreateUrl?real_url=".$link->url,
      array(
        "X-Mashape-Key" => "S60LBMB0ivmshGLcOVyPhT6KTFITp1jjiszjsnQpNmujBNVPuS",
        "Accept" => "application/xml"
      )
    );
    
    $format = $link->type->format;
    $quality = $link->type->quality;
    $shortener = simplexml_load_string($response->raw_body);
    $shortUrl = (string)$shortener->ShortUrl;

    $response = $telegram->sendMessage([
      'chat_id' => $chat->getId(), 
      'text' => "format : $format, quality : $quality, dl-url: $shortUrl",
    ]);

    $messageId = $response->getMessageId();

    echo $messageId;

}


