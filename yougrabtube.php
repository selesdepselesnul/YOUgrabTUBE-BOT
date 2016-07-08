#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
require_once 'Config.php';
use Telegram\Bot\Api;

class YouGrabTube {

    public function __construct($config) {
        
        $this->config = $config;
        $this->telegram = new Api($this->config['telegram_bot_key']);

        $response = $this->telegram->getUpdates(
          ['offset' => -1, 'limit' => 1, 'timeout' => 0]);

        $this->message = $response[0]->getMessage();
        $this->user = $this->message->getFrom();
        $this->chat = $this->message->getChat();
        $this->initConn();

    }

    private function initConn() {
        $this->conn = new PDO(
          "mysql:host=".$this->config['host']
          .";dbname=".$this->config['database']
          , $this->config['user'], 
          $this->config['password']);

        
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function makeUrlShort($url) {
        $response = Unirest\Request::get(
          $this->config['shortener_end_point'].$url,
          [
            "X-Mashape-Key" => $this->config['mashape_key'],
            "Accept" => "application/xml"
          ]
        );  
        $shortener = simplexml_load_string($response->raw_body);
        $shortUrl = (string)$shortener->ShortUrl;
        return $shortUrl;
    }

    private function getNickName() {
        if($this->user->getFirstName() != '' 
          && $this->user->getLastName() != '')
          return $this->user->getLastName();
        
        return $this->user->getFirstName();
    }


    private function sendMessage($message) {
        $lastMessage = 
          $this->telegram->sendMessage([
              'chat_id' => $this->chat->getId(),
              'parse_mode' => 'HTML', 
              'text' => PHP_EOL
                  .$message
                  .PHP_EOL]);
        return $lastMessage;
    }

    private function makeDownloadLinks() {
        $downloadLinks = 
            array_reduce($this->downloadLinks, function($carry, $item) {
                return 
                      PHP_EOL
                      .$carry
                      .'format: '.$item['format']
                      .PHP_EOL.'quality: '.$item['quality']
                      .PHP_EOL.'dl-url: '.$item['url']
                      .PHP_EOL
                      .PHP_EOL;
            });
        return $downloadLinks;
    }

    private function updateLastMessage($table, $values) {
    

          $stmt = $this->conn->prepare(
            "UPDATE {$table}_message  
             SET id = :id,
                 message = :message
             WHERE no = 0;");

          $stmt->bindParam(':id', $values['id']);
          $stmt->bindParam(':message', $values['message']);
          
          $stmt->execute();
    }

    private function getLastMessage($table) {
        $stmt = $this->conn->prepare("SELECT * FROM {$table}_message;");
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result[0];
    }


    public function start() {
        
        $this->updateLastMessage(
            'user',
            [ 
              'id' => $this->message->getMessageId(),
              'message' => $this->message->getText()
            ]
        );
        
        $userLastMessage = $this->getLastMessage('user');
        $botLastMessage = $this->getLastMessage('bot');

        if($botLastMessage['id'] < $userLastMessage['id']) {
            if(preg_match('/https:\/\/www\.youtube\.com\/watch\?v=.+/i', $this->message->getText())) {
            $this->downloadLinks = YoutubeLinkGenerator::generate(
                        $this->config['mashape_key'], 
                        $this->message->getText()); 

            if(empty($this->downloadLinks)) {
                $botMessage = 
                  $this->sendMessage(
                    'yes '.$this->getNickName()
                    .' that was youtube url, but i think that not the valid one'
                    .PHP_EOL.':(');
            } else {

                $headerMessage = 
                      PHP_EOL
                      .'<b>Ok '.$this->getNickName()
                      .', here i give u some links to download the video :</b>'
                      .PHP_EOL;

                $botMessage = $this->sendMessage(
                    $headerMessage
                    .$this->makeDownloadLinks()
                );
            }
            
          } elseif (preg_match('/(please)?(\s)*help(\s)*(me)?/i', $this->message->getText())) {
              $botMessage = 
                $this->sendMessage(
                    'you are such a polite person '.$this->getNickName()
                    .PHP_EOL."ok i'll help you to download youtube video"
                    .PHP_EOL.'to download youtube video you just need to give'
                    .PHP_EOL."an youtube url to me, that's it :)");
          } else if(preg_match('/\/start/i', $this->message->getText())) {
              $botMessage =  
                $this->sendMessage(
                    'what are you waiting for ?'
                    .PHP_EOL
                    ."give me youtube video link and i'll help you :)"
                );
          } else {
              $botMessage = 
                $this->sendMessage(
                    'Hey '.$this->getNickName().' WTF R U talkin bout ?'
                    .PHP_EOL.'do U speak properly, dude ?'
                    .PHP_EOL.'do u want to teach me speak your language ? '
                    .PHP_EOL.'if yes, please contribute to my creator repo => <a href="'.
                    'https://github.com/selesdepselesnul'.'">Moch Deden</a>'
                    .PHP_EOL.'he will glad if you want to contribute :)');
          }

          $this->updateLastMessage(
            'bot',
            [
              'id' => $botMessage->getMessageId(),
              'message' => $botMessage->getText() 
          ]);
  
        }
   
    }

}

$youGrabTube = new YouGrabTube($config);
$youGrabTube->start();

