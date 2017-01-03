<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 22.57
 */

/*
 * Handle incomming terminal arguments.
 */
$terminal_args = [];
require __DIR__."/terminalArgumentsHandler.php";


// Autoload classes
require __DIR__."/autoload.php";


use \StackGuru\BotEvent;

/*
 * Set up the bot
 */
$bot = new \StackGuru\CoreLogic\Bot([
    "discord"   => ST_DISCORD_SETTINGS,
    "database"  => ST_DATABASE_SETTINGS
]);

var_dump(\StackGuru\CoreLogic\Utils\Commands::constructOverviewArray(
    [
        "folder" => __DIR__ . "/src/Commands"
    ]
));


/*
 * Event handlers for different states
 */
$messages_all_including_bot     = function (\Discord\Parts\Channel\Message $message, string $event) 
{
    /*
     * Stuff to be called in this state.
     */
    echo PHP_EOL,"---",PHP_EOL,"$event: $message->content", PHP_EOL;
};

$messages_all_excluding_bot     = function (\Discord\Parts\Channel\Message $message, string $event) 
{
    /*
     * Stuff to be called in this state.
     */
    echo "messages_all_excluding_bot",PHP_EOL;

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
    /*
     * Stuff to be called in this state.
     */
    echo "messages_from_bot",PHP_EOL;
};

$messages_bot_to_bot            = function (\Discord\Parts\Channel\Message $message, string $event) 
{
    /*
     * Stuff to be called in this state.
     */
    echo "messages_bot_to_bot",PHP_EOL;
};

$messages_other_to_bot          = function (\Discord\Parts\Channel\Message $message, string $event) 
{
    /*
     * Stuff to be called in this state.
     */
    echo "messages_other_to_bot",PHP_EOL;
    
    $data = \StackGuru\CoreLogic\Utils\Commands::getCommandInstance($message->content);

    $command = $data["instance"];
    $command = new $command();
    $query = $data["query"];

    //TODO should be updated..
    // dont send a response if the return is null.
    $response = $command->process($query);
    if ($response !== null) {
        \StackGuru\CoreLogic\Utils\Response::sendResponse($response, $message);
    }
};



/*
 * Add listeners
 */
$bot->state(\StackGuru\BotEvent::MESSAGE_ALL_I_SELF,           $messages_all_including_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_ALL_E_SELF,           $messages_all_excluding_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_FROM_SELF,            $messages_from_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_SELF_TO_SELF,         $messages_bot_to_bot);
$bot->state(\StackGuru\BotEvent::MESSAGE_OTHERS_TO_SELF,       $messages_other_to_bot);


/*
 * Run the bot
 */
$bot->run();
