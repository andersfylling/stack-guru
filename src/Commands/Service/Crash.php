<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Crash extends AbstractCommand
{
    protected static $name = "crash";
    protected static $description = "something about the shutdown command";


    public function process (string $query, ?CommandContext $ctx) : string
    {
        $args = explode(' ', trim($query) . ' ');
        return "gkdfjhlg_crash";
    }
}
