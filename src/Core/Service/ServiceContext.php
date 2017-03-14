<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;


class ServiceContext
{
    public $bot;         	// \StackGuru\Core\Bot
    public $guild;		 	// guild reference
    public $cmdRegistry; 	// \StackGuru\Core\Command\Registry
    public $services;		// array of services
    public $parent;      	// \StackGuru\Core\Command\CommandInterface
    public $message;     	// \Discord\Parts\Channel\Message
    public $discord;	 	// Discord
    public $commandEntry;
}
