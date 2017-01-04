<?php

namespace StackGuru\Commands\Help;

class Help extends \StackGuru\Command
{
    const COMMAND_NAME = "help";
    const DESCRIPTION = "returns a list of available bot commands";
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
            $mainCommand = $subcommands[$command];
            unset($subcommands[$command]);

            // Print command and subcommands
            $cmdline = "* !{$command}";
            if (sizeof($subcommands) > 0) {
                $cmdline .= " [" . implode(", ", array_keys($subcommands)) ."]";
            }

            // Pad command names to align command descriptions
            $cmdline = sprintf("%-40s", $cmdline);

            // Add command description
            $description = $mainCommand::DESCRIPTION;
            if (!empty($description)) {
                $cmdline .= " - {$description}";
            }

            $helptext .= $cmdline . "\n";
        }
        $helptext .= "```";

        return $helptext;
    }
}
