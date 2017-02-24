<?php

// Make sure that the php version is correct
const MIN_PHP_VERSION = "7.1.0";
if (version_compare(PHP_VERSION, MIN_PHP_VERSION) < 0) {
    echo "Your php version is too old: ", phpversion(), PHP_EOL;
    echo "Please upgrade to version or higher: ", MIN_PHP_VERSION, PHP_EOL;
    exit;
}

// Handle incomming terminal arguments.
$terminal_args = [];
require __DIR__."/terminalArgumentsHandler.php";

// Autoload classes
require __DIR__."/autoload.php";


// Start of bot logic
use \StackGuru\Core\BotEvent;
use \StackGuru\Core\Utils;


// Setup bot instance
$bot = new \StackGuru\Core\Bot([
    "discord"           => ST_DISCORD_SETTINGS,
    "database"          => ST_DATABASE_SETTINGS,
    "loadAllMembers"    => true
]);

// Run the bot
$bot->run();
