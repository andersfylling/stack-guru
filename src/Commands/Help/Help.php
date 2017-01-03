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
        // Encapsulate command list in code block
        $helptext = "```markdown\n";

        $helptext .= "# Available commands\n";

        // Print all commands
        $commands = $ctx->cmdRegistry->getAll();
        foreach ($commands as $command => $subcommands) {
            // Remove main command from subcommands
            unset($subcommands[$command]);

            $cmdline = "* !{$command}";
            if (sizeof($subcommands) > 0) {
                $cmdline .= " [" . implode(", ", array_keys($subcommands)) ."]";
            }
            $helptext .= $cmdline . "\n";
        }
        $helptext .= "```";

        return $helptext;
    }
}
