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


// Event handlers for different states
$messages_all_including_bot     = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "---", PHP_EOL, "$event: $message->content", PHP_EOL;
};

$messages_all_excluding_bot     = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "messages_all_excluding_bot", PHP_EOL;
    
    // yo no habla Española
    if (null !== $message->content && '¡' == StackGuru\Core\Utils\StringParser::getCharAt(0, $message->content)) {
        $spanishResponse = [
            "No hablo español, lo siento"
        ];

        $response = $spanishResponse[array_rand($spanishResponse, 1)];
        Utils\Response::sendResponse($response, $message);
    }
};

$messages_all_excluding_bot_command     = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "messages_all_excluding_bot_command", PHP_EOL;

    // Be rude to people who say NZT
    
};

$messages_from_bot              = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "messages_from_bot", PHP_EOL;
};

$messages_bot_to_bot            = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "messages_bot_to_bot", PHP_EOL;
};

$messages_other_to_bot          = function(\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    //echo "messages_other_to_bot", PHP_EOL;
};


// Add callbacks
$bot->state(BotEvent::MESSAGE_ALL_I_SELF,           $messages_all_including_bot);
$bot->state(BotEvent::MESSAGE_ALL_E_SELF,           $messages_all_excluding_bot);
$bot->state(BotEvent::MESSAGE_ALL_E_COMMAND,        $messages_all_excluding_bot_command);
$bot->state(BotEvent::MESSAGE_FROM_SELF,            $messages_from_bot);
$bot->state(BotEvent::MESSAGE_SELF_TO_SELF,         $messages_bot_to_bot);
$bot->state(BotEvent::MESSAGE_OTHERS_TO_SELF,       $messages_other_to_bot);


// Run the bot
$bot->run();
