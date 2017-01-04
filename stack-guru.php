<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 22.57
 */

// Make sure that the php version is correct
// echo PHP_VERSION_ID;
if (701000 > PHP_VERSION_ID) { // 7.1
    echo "Your php version is too old: ", phpversion(), PHP_EOL;
    echo "Please upgrade to version or higher: 7.1", PHP_EOL;
    exit;
}


// Handle incomming terminal arguments.
$terminal_args = [];
require __DIR__."/terminalArgumentsHandler.php";


// Autoload classes
require __DIR__."/autoload.php";





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
        \StackGuru\CoreLogic\Utils\Response::sendResponse($response, $message);
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
        \StackGuru\CoreLogic\Utils\Response::sendResponse("I'm sorry. It seems I cannot find your command. Please try the command: help", $message);
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
