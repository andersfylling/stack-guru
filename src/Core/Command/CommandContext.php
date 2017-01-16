<?php

namespace StackGuru\Core\Command;


class CommandContext
{
    public $bot;         // \StackGuru\Core\Bot
    public $cmdRegistry; // \StackGuru\Core\Command\Registry
    public $parent;      // \StackGuru\Core\Command\CommandInterface
    public $message;     // \Discord\Parts\Channel\Message
}
