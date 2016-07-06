#!/usr/bin/env php
<?php
// author : Moch Deden
require __DIR__ . '/vendor/autoload.php';
require_once 'YoutubeLinkGenerator.php';
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
            

    }

    private function makeUrlShort($url) {
        $response = Unirest\Request::get(
          $this->config['shortener_end_point'].$url,
          array(
            "X-Mashape-Key" => $this->config['mashape_key'],
            "Accept" => "application/xml"
          )
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
        $this->telegram->sendMessage([
          'chat_id' => $this->chat->getId(),
          'parse_mode' => 'HTML', 
          'text' => PHP_EOL
              .$message
              .PHP_EOL]);
    }

    private function sendDownloadLinks() {
        foreach ($this->downloadLinks as $i => $downloadLink) {
            $this->sendMessage('<b>'.($i + 1).'. </b><a href="'
                  .$this->makeUrlShort($downloadLink['url']).'">'
                  .$downloadLink['format'].' '.$downloadLink['quality']
                  .'</a>');
        }
    }

    public function start() {
 
        if(preg_match('/https:\/\/www\.youtube\.com\/watch\?v=.+/i', $this->message->getText())) {
            $this->downloadLinks = YoutubeLinkGenerator::generate(
                        $this->config['mashape_key'], 
                        $this->message->getText()); 

            $this->sendMessage(
              'Ok '.$this->getNickName()
              .', here i give u some links to download the video :');

            $this->sendDownloadLinks();
          
            $this->sendMessage('click the link and klik ok when telegram ask you !');  
        } else {
            $this->sendMessage(
              '<b>WTF r u talkin bout ?'
              .PHP_EOL.'do u speak properly dude ?</b>');
        }

    }

}

$youGrabTube = new YouGrabTube($config);
$youGrabTube->start();

