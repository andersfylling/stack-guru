<?php

namespace StackGuru\Commands\Service;

class Shutdown extends Service implements \StackGuru\CommandInterface
{
    const COMMAND_NAME = "shutdown";
    const DESCRIPTION = "something about the shutdown command";

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');
        return "gkdfjhlg";
    }
}