<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

/**
 * Remove a service from the database entry.
 */
class Deactivate extends AbstractCommand
{
    protected static $name = "deactivate";
    protected static $description = "Deactivates a command so it can't be run";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "";
    }
}
