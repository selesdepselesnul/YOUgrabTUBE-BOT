#!/usr/bin/env php
<?php
/**
 * @author : Moch Deden
 * @site : http://selesdepselesnul.com 
 */
require __DIR__ . '/vendor/autoload.php';
require_once 'Config.php';
use Telegram\Bot\Api;

class YouGrabTube {

    public function __construct($config) {
        
        $this->config = $config;
        $this->telegram = new Api($this->config['telegram_bot_key']);
        $this->youtubeLinkGenerator = new YoutubeLinkGenerator($config['mashape_key']);
        $this->youtubeUrlParser = new YoutubeUrlParser;

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

    private function sendDownloadLink() {
        $headerMessage = 
            PHP_EOL
            .'<b>Ok '.$this->getNickName()
            .', here i give u some links to download the video :</b>'
            .PHP_EOL;
        var_dump($this->makeDownloadLinks());      
        $botMessage = $this->sendMessage(
            $headerMessage
            .$this->makeDownloadLinks()
        );
        return $botMessage;
    }

    private function processVideoUrl($videoUrl) {
        $this->downloadLinks = $this->youtubeLinkGenerator->generate($videoUrl);

        if(empty($this->downloadLinks)) 
            $botMessage = 
                $this->sendMessage(
                  'yes '.$this->getNickName()
                  .' that was youtube url, but i think that not the valid one'
                  .PHP_EOL.':(');
        else
            $botMessage = $this->sendDownloadLink();
        return $botMessage;
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
        $userText = $this->message->getText();

        if($botLastMessage['id'] < $userLastMessage['id']) {

            if(preg_match(YoutubeUrlParser::SHORT_URL, $userText)) 
                $videoUrl = $this->youtubeUrlParser->parseShort($userText);
            else if(preg_match(YoutubeUrlParser::LONG_URL, $userText)) 
                $videoUrl = $this->youtubeUrlParser->parseLong($userText);
            else
                $videoUrl = null;

            if(is_null($videoUrl)) 
                $botMessage = 
                  $this->sendMessage(
                    "That's not even an youtube url !");
            else 
                $botMessage = $this->processVideoUrl($videoUrl);
             

            $this->updateLastMessage(
                'bot',
                [
                  'id' => $botMessage->getMessageId(),
                  'message' => $botMessage->getText() 
                ]
            );
        }
    }
}

$youGrabTube = new YouGrabTube($config);
$youGrabTube->start();

