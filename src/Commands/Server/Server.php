<?php

namespace StackGuru\Commands\Server;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Server extends AbstractCommand
{
    protected static $name = "server";
    protected static $description = "bot service commands";
    protected static $default = "status";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Not implemented yet";
    }
}
