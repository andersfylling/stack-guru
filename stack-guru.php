<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 22.57
 */

// Handle incomming terminal arguments.
$terminal_args = [];
require __DIR__."/terminalArgumentsHandler.php";


// Autoload classes
require __DIR__."/autoload.php";

error_reporting( E_ALL );
ini_set('display_errors', 1);

use \StackGuru\BotEvent;
use \StackGuru\CoreLogic\Utils;


// Setup bot instance
$bot = new \StackGuru\CoreLogic\Bot([
    "discord"   => ST_DISCORD_SETTINGS,
    "database"  => ST_DATABASE_SETTINGS
]);

// Setup command registry.
const DEFAULT_COMMANDS_FOLDER = __DIR__ . "/src/Commands";
$cmdRegistry = new \StackGuru\CommandRegistry([DEFAULT_COMMANDS_FOLDER]);

// Debug output
{
    $commands = $cmdRegistry->getAll();
    echo "Loaded ", sizeof($commands), " commands:", PHP_EOL;
    foreach ($commands as $command => $subcommands) {
        echo " * ", $command, " [", implode(", ", array_keys($subcommands)), "]", PHP_EOL;
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

    echo "Processing message: ", $message->content, PHP_EOL;

    // Parse query to find the command instance and get the remaining arguments.
    $data = $cmdRegistry->parseQuery($message->content);
    var_dump($data);

    $command = $data["instance"];
    if ($command === null) {
        \StackGuru\CoreLogic\Utils\Response::sendResponse("I'm sorry. It seems I cannot find your command. Please try the command: help", $message);
        return;
    }
    $query = $data["query"];

    // Build command context so the command has references back to the bot
    // and other commands.
    $context = new \StackGuru\CommandContext();
    $context->bot = $bot;
    $context->message = $message;

    // Run command and don't send a response if the return is null.
    echo "a", PHP_EOL;
    $response = $command->process($query);
    var_dump(array('response' => $response));
    if ($response !== null) {
        echo "Trying to send response:", PHP_EOL;
        var_dump(array('response' => $response, 'message' => $message));
        \StackGuru\CoreLogic\Utils\Response::sendResponse($response, $message);
    }
};



// Add listeners
$bot->state(\StackGuru\BotEvent::MESSAGE_ALL_I_SELF,           $messages_all_including_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_ALL_E_SELF,           $messages_all_excluding_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_FROM_SELF,            $messages_from_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_SELF_TO_SELF,         $messages_bot_to_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_OTHERS_TO_SELF,       $messages_other_to_bot);


// Run the bot
$bot->run();
