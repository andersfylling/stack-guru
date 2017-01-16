<?php

namespace StackGuru\Commands\Help;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Command\CommandEntry;


class Help extends AbstractCommand
{
    protected static $name = "help";
    protected static $description = "returns a list of available bot commands";


    public function process(string $query, ?CommandContext $ctx): string
    {
        // Encapsulate command list in code block
        $helptext = "```markdown\n";

        $helptext .= "# Available commands\n";

        // Print all commands
        $commands = $ctx->cmdRegistry->getCommands();
        foreach ($commands as $name => $command) {
            $commandTree = self::getCommandTreeString($command);

            // Print command with subcommand tree
            $cmdline = "* !{$commandTree}";

            // Pad command names to align command descriptions
            $cmdline = sprintf("%-40s", $cmdline);

            // Add command description
            $description = $command->getDescription();
            if (!empty($description)) {
                $cmdline .= " - {$description}";
            }

            $helptext .= $cmdline . "\n";
        }
        $helptext .= "```";

        return $helptext;
    }

    private static function getCommandTreeString(CommandEntry $command): string
    {
        $str = $command->getName();
        $children = $command->getChildren();
        if (sizeof($children) > 0) {
            $subtrees = [];
            foreach ($children as $name => $child)
                $subtrees[] = self::getCommandTreeString($child);
            $str .= " [" . implode(", ", $subtrees) ."]";
        }
        return $str;
    }
}
