<?php

namespace StackGuru;

class CommandContext
{
    public $bot; // \StackGuru\CoreLogic\Bot
    public $cmdRegistry; // \StackGuru\CommandRegistry
    public $parent; // \StackGuru\CommandInterface
    public $message; // \Discord\Parts\Channel\Message
}
