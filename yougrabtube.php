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
$query = parse_url($message->getText())['query'];
parse_str($query, $arrQuery);
$videoId = $arrQuery['v'];
$response = Unirest\Request::get($config['downloader_end_point'].$videoId,
  [
    "X-Mashape-Key" => $config['mashape_key'],
    "Accept" => "application/json"
  ]
);

$links = $response->body->link;

for ($i=0; $i < count($links)-1 ; $i++) { 
  $link = $links[$i];
  $response = Unirest\Request::get(
      $config['shortener_end_point'].$link->url,
      array(
        "X-Mashape-Key" => $config['mashape_key'],
        "Accept" => "application/xml"
      )
    );
    
    var_dump($link);
    $type = $link->type;
    $format = $type->format;
    $quality = $type->quality;
    $shortener = simplexml_load_string($response->raw_body);
    $shortUrl = (string)$shortener->ShortUrl;

    if($i == 0)
      $response = $telegram->sendMessage([
        'chat_id' => $chat->getId(), 
        'text' => "Enjoy your video !",
      ]);
  
    $response = $telegram->sendMessage([
      'chat_id' => $chat->getId(), 
      'text' => "format : $format, quality : $quality, dl-url: $shortUrl",
    ]);

    $messageId = $response->getMessageId();

    echo $messageId;
}
// foreach ($links as $link) {
    
//     $response = Unirest\Request::get(
//       $config['shortener_end_point'].$link->url,
//       array(
//         "X-Mashape-Key" => $config['mashape_key'],
//         "Accept" => "application/xml"
//       )
//     );
    
//     $type = $link->type;
//     $format = $type->format;
//     $quality = $type->quality;
//     $shortener = simplexml_load_string($response->raw_body);
//     $shortUrl = (string)$shortener->ShortUrl;

//     $response = $telegram->sendMessage([
//       'chat_id' => $chat->getId(), 
//       'text' => "format : $format, quality : $quality, dl-url: $shortUrl",
//     ]);

//     $messageId = $response->getMessageId();

//     echo $messageId;

// }


