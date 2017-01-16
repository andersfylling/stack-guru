<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Service extends AbstractCommand
{
    protected static $name = "service";
    protected static $description = "bot service commands";
    protected static $default = "shutdown";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Not implemented yet";
    }
}
