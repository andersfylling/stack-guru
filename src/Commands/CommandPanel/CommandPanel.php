<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class CommandPanel extends AbstractCommand
{
    protected static $name = "commandpanel";
    protected static $description = "bot service commands";
    protected static $default = "";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Not implemented yet";
    }
}
