<?php

namespace StackGuru\Commands\Service;

class Crash extends Service implements \StackGuru\CommandInterface
{
    const COMMAND_NAME = "shutdown";
    const DESCRIPTION = "something about the shutdown command";

    public function process (array $args = [], \StackGuru\CommandContext $ctx = null) : string
    {
        return "gkdfjhlg_crash";
    }
}