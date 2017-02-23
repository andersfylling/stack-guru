<?php
declare(strict_types=1);

namespace StackGuru\Core\Command;


class CommandContext
{
    public $bot;         // \StackGuru\Core\Bot
    public $guild;		 // guild reference
    public $cmdRegistry; // \StackGuru\Core\Command\Registry
    public $parent;      // \StackGuru\Core\Command\CommandInterface
    public $message;     // \Discord\Parts\Channel\Message
    public $discord;	 // Discord
    public $parentCommand; // parent command of current command, assuming a command can only have one subcommand. might be null.
}
