<?php

namespace StackGuru\Commands\Help;

class Help extends \StackGuru\Commands\BaseCommand
{
    const COMMAND_NAME = "help";
    const DESCRIPTION = "Returns a list of available bot commands";
    const DEFAULT = "help";

    public function __construct()
    {
        parent::__construct();
    }

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        $helptext = "Here is a list of available commands:\n";
        $commands = $ctx->cmdRegistry->getAll();
        foreach ($commands as $command => $subcommands) {
            $helptext .= sprintf("* !%s [%s]\n", $command, implode(", ", array_keys($subcommands)));
        }

        return $helptext;
    }
}
