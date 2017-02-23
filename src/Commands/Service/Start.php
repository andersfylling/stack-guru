<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Start extends AbstractCommand
{
    protected static $name = "start";
    protected static $description = "something about the shutdown command";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Starting...";
    }
}
