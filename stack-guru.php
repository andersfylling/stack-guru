<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 22.57
 */

 define("MIN_PHP_VERSION", "7.1.0");

 // Make sure that the php version is correct
 $version = explode('.', MIN_PHP_VERSION);
 define('MIN_PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
 if (PHP_VERSION_ID < MIN_PHP_VERSION_ID) {
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
    "discord"   => ST_DISCORD_SETTINGS,
    "database"  => ST_DATABASE_SETTINGS
]);

// Setup command registry.
$cmdRegistry = new \StackGuru\Core\Command\Registry();
$cmdRegistry->loadCommandFolder(__DIR__ . "/src/Commands", "StackGuru\\Commands");

// Debug output
{
    $commands = $cmdRegistry->getCommands();
    echo "Loaded ", sizeof($commands), " commands:", PHP_EOL;
    foreach ($commands as $name => $command) {
        echo " * ", $name, " [", implode(", ", array_keys($command->subcommands)), "]", PHP_EOL;
    }
    echo PHP_EOL;
}

// Event handlers for different states
$messages_all_including_bot     = function (\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    echo "---", PHP_EOL, "$event: $message->content", PHP_EOL;
};

$messages_all_excluding_bot     = function (\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    echo "messages_all_excluding_bot", PHP_EOL;

    // Be rude to people who say NZT
    if (strpos(strtolower($message->content), "nzt") !== false) {
        $rudeNZTResponses = [
            "Really.. NZT!?",
            "NZT? Who do you think you are!?",
            "NZT? Shut up human!",
            "NZT!? You silly little creature.",
            "NZT? Blæ?",
            "How about no more NZT you humans"
        ];

        $response = $rudeNZTResponses[array_rand($rudeNZTResponses, 1)];
        Utils\Response::sendResponse($response, $message);
    }
};

$messages_from_bot              = function (\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    echo "messages_from_bot", PHP_EOL;
};

$messages_bot_to_bot            = function (\Discord\Parts\Channel\Message $message, string $event)
{
    // Stuff to be called in this state.
    echo "messages_bot_to_bot", PHP_EOL;
};

$messages_other_to_bot          = function (\Discord\Parts\Channel\Message $message, string $event) use ($bot, $cmdRegistry)
{
    // Stuff to be called in this state.
    echo "messages_other_to_bot", PHP_EOL;

    // Parse query to find the command instance and get the remaining arguments.
    $data = $cmdRegistry->parseQuery($message->content);

    $command = $data["instance"];
    if ($command === null) {
        Utils\Response::sendResponse("I'm sorry. It seems I cannot find your command. Please try the command: help", $message);
        return;
    }
    $query = $data["query"];

    // Build command context so the command has references back to the bot
    // and other commands.
    $context = new \StackGuru\CommandContext();
    $context->bot = $bot;
    $context->cmdRegistry = $cmdRegistry;
    $context->message = $message;

    // Run command and send a response if the return is not null.
    $response = $command->process($query, $context);
    if ($response !== null) {
        Utils\Response::sendResponse($response, $message);
    }
};


// Add callbacks
$bot->state(BotEvent::MESSAGE_ALL_I_SELF,           $messages_all_including_bot);
$bot->state(BotEvent::MESSAGE_ALL_E_SELF,           $messages_all_excluding_bot);
$bot->state(BotEvent::MESSAGE_FROM_SELF,            $messages_from_bot);
$bot->state(BotEvent::MESSAGE_SELF_TO_SELF,         $messages_bot_to_bot);
$bot->state(BotEvent::MESSAGE_OTHERS_TO_SELF,       $messages_other_to_bot);


// Run the bot
$bot->run();
