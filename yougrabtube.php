#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

$API_KEY = 'your_bot_api_key';
$BOT_NAME = 'namebot';
$mysql_credentials = [
   'host'     => 'localhost',
   'user'     => 'dbuser',
   'password' => 'dbpass',
   'database' => 'dbname',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Enable MySQL
    $telegram->enableMySQL($mysql_credentials);

    // Handle telegram getUpdate request
    $telegram->handleGetUpdates();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e;
}
