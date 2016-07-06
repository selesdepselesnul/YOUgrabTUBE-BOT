#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
require_once 'YoutubeLinkGenerator.php';
require_once 'Config.php';
use Telegram\Bot\Api;

function makeUrlShort($url) {
    global $config;
    $response = Unirest\Request::get(
      $config['shortener_end_point'].$url,
      array(
        "X-Mashape-Key" => $config['mashape_key'],
        "Accept" => "application/xml"
      )
    );  
    $shortener = simplexml_load_string($response->raw_body);
    $shortUrl = (string)$shortener->ShortUrl;
    return $shortUrl;
}
// function processMessage() {
//   $query = parse_url($userMessage)['query'];
//   parse_str($query, $arrQuery);
//   $videoId = $arrQuery['v'];
//   $response = Unirest\Request::get($config['downloader_end_point'].$videoId,
//     [
//       "X-Mashape-Key" => $config['mashape_key'],
//       "Accept" => "application/json"
//     ]
//   );

//   $links = $response->body->link;

//   if($user->getFirstName() != '' 
//     && $user->getLastName() != '')
//     $name = $user->getLastName();
//   else 
//     $name = $user->getFirstName();

//   $response = $telegram->sendMessage([
//         'chat_id' => $chat->getId(), 
//         'text' => 'These are the links i could give to you, '.$name.', enjoy !',
//   ]);
//   for ($i=0; $i < count($links)-1 ; $i++) { 
//     $link = $links[$i];
//     $response = Unirest\Request::get(
//         $config['shortener_end_point'].$link->url,
//         array(
//           "X-Mashape-Key" => $config['mashape_key'],
//           "Accept" => "application/xml"
//         )
//       );
      
//       var_dump($link);
//       $type = $link->type;
//       $format = $type->format;
//       $quality = $type->quality;
//       $shortener = simplexml_load_string($response->raw_body);
//       $shortUrl = (string)$shortener->ShortUrl;
       
    
//       $response = $telegram->sendMessage([
//         'chat_id' => $chat->getId(), 
//         'text' => "format : $format, quality : $quality, dl-url: $shortUrl",
//       ]);

//       $messageId = $response->getMessageId();

//       echo $messageId;
//   }

//   $response = $telegram->sendMessage([
//         'chat_id' => $chat->getId(), 
//         'text' => "open those links in new tab, then right click and click save as !",
//   ]);  
// }


$telegram = new Api($config['telegram_bot_key']);

$response = $telegram->getUpdates(
  ['offset' => -1, 'limit' => 1, 'timeout' => 0]);


$message = $response[0]->getMessage();
$user = $message->getFrom();
$chat = $message->getChat();
$userMessage = $message->getText();

var_dump($userMessage);
    
$downloadLinks = YoutubeLinkGenerator::generate($config['mashape_key'], 
                                                        $userMessage);

foreach ($downloadLinks as $i => $downloadLink) {
    $telegram->sendMessage([
      'chat_id' => $chat->getId(),
      'parse_mode' => 'HTML', 
      'text' => PHP_EOL
          .'<b>'.($i + 1).'. </b><a href="'.makeUrlShort($downloadLink['url']).'">'
          .$downloadLink['format'].' '.$downloadLink['quality']
          .'</a>'.PHP_EOL
    ]);      
}


