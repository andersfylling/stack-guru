<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;

/**
 * Remove a service from the database entry.
 */
class Disable extends AbstractCommand
{
    protected static $name = "disable";
    protected static $description = "Disable a service so it won't run at boot";


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Disabling...";
    }
}
