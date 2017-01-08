<?php

namespace StackGuru\Commands\Service;

class Crash extends \StackGuru\Commands\BaseCommand
{
    protected static $name = "shutdown";
    protected static $description = "something about the shutdown command";


    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');
        return "gkdfjhlg_crash";
    }
}
