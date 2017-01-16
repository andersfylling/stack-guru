<?php

namespace StackGuru\Commands\Drug;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Drug extends AbstractCommand
{
    protected static $name = "drug";
    protected static $description = "find drug information";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        return "Not implemented yet";
    }
}
