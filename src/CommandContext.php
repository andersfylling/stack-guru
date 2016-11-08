<?php

namespace StackGuru;

class CommandContext
{
    public $bot; // \StackGuru\CoreLogic\Bot
    public $message; // \Discord\Parts\Channel\Message
    public $parent; // \StackGuru\CommandInterface

    public function __construct()
    {
    }
}
