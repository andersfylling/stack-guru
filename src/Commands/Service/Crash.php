<?php

namespace StackGuru\Commands\Service;

class Crash extends \StackGuru\Command implements \StackGuru\CommandInterface
{
    protected $name = "shutdown";
    protected $description = "something about the shutdown command";


    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');
        return "gkdfjhlg_crash";
    }
}
