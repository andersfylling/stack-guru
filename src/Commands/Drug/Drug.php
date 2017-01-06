<?php

namespace StackGuru\Commands\Drug;

class Drug extends \StackGuru\Command
{
    protected $name = "drug";
    protected $description = "find drug information";
    protected $default = "info"; // default command


    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
