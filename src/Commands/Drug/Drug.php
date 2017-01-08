<?php

namespace StackGuru\Commands\Drug;

class Drug extends \StackGuru\Commands\BaseCommand
{
    protected static $name = "drug";
    protected static $description = "find drug information";
    protected static $default = "info"; // default sub-command


    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
