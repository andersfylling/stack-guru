<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 22.57
 */

/*
 * Includes
 */
require __DIR__.'/vendor/autoload.php';

require "./src/AutoLoader.php";

require "./config/discord.php";

/*
 * Setup Auto loader
 */
$loader = new \StackGuru\AutoLoader;
$loader->register();
$loader->addNamespace('StackGuru', __DIR__.'/src');

/*
 * Set up the bot
 */
use \StackGuru\BotEvent;
$bot = new \StackGuru\CoreLogic\Bot();


/*
 * Event handlers for different states
 */
$messages_all_including_bot = function () {
    /*
     * Stuff to be called in this state.
     */
};

$messages_all_excluding_bot = function () {
    /*
     * Stuff to be called in this state.
     */
};

$messages_from_bot = function () {
    /*
     * Stuff to be called in this state.
     */
};

$messages_bot_to_bot = function () {
    /*
     * Stuff to be called in this state.
     */
};

$messages_other_to_bot = function () {
    /*
     * Stuff to be called in this state.
     */
};




/*
 * Add listeners
 */
$bot->state(BotEvent::MESSAGE_ALL_I_SELF,       $messages_all_including_bot);
$bot->state(BotEvent::MESSAGE_ALL_E_SELF,       $messages_all_excluding_bot);
$bot->state(BotEvent::MESSAGE_FROM_SELF,        $messages_from_bot);
$bot->state(BotEvent::MESSAGE_SELF_TO_SELF,     $messages_bot_to_bot);
$bot->state(BotEvent::MESSAGE_OTHERS_TO_SELF,   $messages_other_to_bot);

/*
 * Run the bot
 */
$bot->run();
