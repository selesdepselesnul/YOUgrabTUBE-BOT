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
        $this->telegram->sendMessage([
          'chat_id' => $this->chat->getId(),
          'parse_mode' => 'HTML', 
          'text' => PHP_EOL
              .$message
              .PHP_EOL]);
    }

    private function makeDownloadLinks() {
        $downloadLinks = 
            array_reduce($this->downloadLinks, function($carry, $item) {
                return $carry
                      .'<a href="'.$this->makeUrlShort($item['url']).'">'
                      .$item['format'].' '.$item['quality']
                      .'</a>'
                      .PHP_EOL;
            });
        return $downloadLinks;
        // foreach ($this->downloadLinks as $i => $downloadLink) {
        //     $this->sendMessage('<b>'.($i + 1).'. </b><a href="'
        //           .$this->makeUrlShort($downloadLink['url']).'">'
        //           .$downloadLink['format'].' '.$downloadLink['quality']
        //           .'</a>');
        // }
    }

    public function start() {
 
        if(preg_match('/https:\/\/www\.youtube\.com\/watch\?v=.+/i', $this->message->getText())) {
            $this->downloadLinks = YoutubeLinkGenerator::generate(
                        $this->config['mashape_key'], 
                        $this->message->getText()); 
            if(empty($this->downloadLinks)) {
                $this->sendMessage('yes '.$this->getNickName()
                  .' that was youtube url, but i think that not the valid one :(');
            } else {
                $headerMessage = 
                      PHP_EOL
                      .'Ok '.$this->getNickName()
                      .', here i give u some links to download the video :'
                      .PHP_EOL;

                $footerMessage = 'click the link and klik ok when telegram ask you !'.PHP_EOL;
                
                $this->sendMessage(
                    $headerMessage
                    .$this->makeDownloadLinks()
                    .$footerMessage
                );
    
            }
            
        } elseif (preg_match('/(please)?(\s)*help(\s)*(me)?/i', $this->message->getText())) {
            $this->sendMessage(
              'you are such a polite person !');
            $this->sendMessage(
              'ok will help you to download youtube video');
            $this->sendMessage(
              'to download youtube video you just need to give');
            $this->sendMessage(
              "an youtube url to me, that's it :)");
        } else {
            $this->sendMessage(
              '<b>WTF R U talkin bout ?'
              .PHP_EOL.'do U speak properly, dude ?</b>');
            $this->sendMessage(
              'do u want to teach me speak your language ? '
              .PHP_EOL.'if yes, please contribute to my creator repo => <a href="'.
              'https://github.com/selesdepselesnul'.'">Moch Deden</a>'
              .PHP_EOL.'he will glad if you want to contribute :)');
        }

    }

}

$youGrabTube = new YouGrabTube($config);
$youGrabTube->start();

