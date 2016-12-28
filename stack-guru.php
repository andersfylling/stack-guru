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

    "database"  => ST_DATABASE_SETTINGS,

    "commands"  => [
        "folder" => __DIR__ . "/src/Commands"
    ]

]);


/*
 * Event handlers for different states
 */
$messages_all_including_bot     = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
    echo "$event: ", $message->content, PHP_EOL;
};

$messages_all_excluding_bot     = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
};

$messages_from_bot              = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
};

$messages_bot_to_bot            = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
};

$messages_other_to_bot          = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
};


$messages_other_to_bot_ready    = function (
        \Discord\Parts\Channel\Message $message,
        string $event,
        array $command = null
) {
    /*
     * Stuff to be called in this state.
     */
    

    /*
     * Initiate command
     */
    if (null !== $command) {
        $command = $this->commands[$command["command"]];

        $context = new \StackGuru\CommandContext();
        $context->bot = $this;
        $context->message = $message;

        $command->process($command["arguments"], $context);
    }
};



/*
 * Add listeners
 */
$bot->state(BotEvent::MESSAGE_ALL_I_SELF,           $messages_all_including_bot);
$bot->state(BotEvent::MESSAGE_ALL_E_SELF,           $messages_all_excluding_bot);
$bot->state(BotEvent::MESSAGE_FROM_SELF,            $messages_from_bot);
$bot->state(BotEvent::MESSAGE_SELF_TO_SELF,         $messages_bot_to_bot);
$bot->state(BotEvent::MESSAGE_OTHERS_TO_SELF,       $messages_other_to_bot);
$bot->state(BotEvent::MESSAGE_OTHERS_TO_SELF_READY, $messages_other_to_bot_ready);


/*
 * Run the bot
 */
$bot->run();
