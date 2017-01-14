<?php

namespace StackGuru\Commands\Help;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Help extends AbstractCommand
{
    protected static $name = "help";
    protected static $description = "returns a list of available bot commands";


    public function process (string $query, ?CommandContext $ctx) : string
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
