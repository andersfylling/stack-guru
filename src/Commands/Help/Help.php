<?php

namespace StackGuru\Commands\Help;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Command\CommandEntry;
use StackGuru\Core\Utils\StringParser;


class Help extends AbstractCommand
{
    protected static $name = "help";
    protected static $description = "returns a list of available bot commands";


    public function process(string $query, ?CommandContext $ctx): string
    {
        // all the commands
        $commands = $ctx->cmdRegistry->getCommands();

        // Encapsulate command list in code block
        $helptext = "```markdown" . PHP_EOL;

        // Decide if all commands should be shown, just one command or a subcommand.
        // also mention how a service is used.
        
        $keys = StringParser::getFirstWords($query, 2);

        // a sub command
        if (isset($keys[1]) && isset($commands[$keys[0]]) && isset($commands[$keys[0]]->getChildren()[$keys[1]])) {
            $command = $commands[$keys[0]];
            $subcommand = $command->getChildren()[$keys[1]];


            $helptext .= "# Command name" . PHP_EOL;
            $helptext .= "* {$command->getName()} {$subcommand->getName()}" . PHP_EOL . PHP_EOL;

            self::showCommandUsage($helptext, [$subcommand->getName()]);

            $helptext .= "# Description" . PHP_EOL;
            $helptext .= "* {$subcommand->getDescription()}" . PHP_EOL . PHP_EOL;
        } 

        // A main command
        else if (isset($keys[0]) && isset($commands[$keys[0]])) {
            $command = $commands[$keys[0]];


            $helptext .= "# Command name" . PHP_EOL;
            $helptext .= "* {$command->getName()}" . PHP_EOL . PHP_EOL;

            self::showCommandUsage($helptext, [$command->getName()]);

            $helptext .= "# Description" . PHP_EOL;
            $helptext .= "* {$command->getDescription()}" . PHP_EOL . PHP_EOL;


            // Print all subcommands
            $helptext .= "# Sub-commands" . PHP_EOL;
            $children = $command->getChildren();
            foreach ($children as $name => $child) {

                // Print command with subcommand tree
                $cmdline = "* !{$command->getName()} {$child->getName()}";

                // Pad command names to align command descriptions
                $cmdline = sprintf("%-30s", $cmdline);

                // Add command description
                $description = $child->getDescription();
                if (!empty($description)) {
                    $cmdline .= " - {$description}";
                }

                $helptext .= $cmdline . PHP_EOL;
            }


        } 
        

        // default, all commands
        else {
            self::showCommandUsage($helptext, ["command", "subcommand"]);

            self::showMainCommands($helptext, $ctx);
        }
        

        $helptext .= "```";

        return $helptext;
    }

    private static function showCommandUsage(string &$helptext, $arr) : void 
    {
        // Show command/subcommand syntax.
        $helptext .= "# Usage" . PHP_EOL;

        for ($i = 0; $i < sizeof($arr); $i++) {

            $helptext .= "* !";

            for ($j = 0; $j <= $i; $j++) {
                $helptext .= $arr[$j] . ' ';
            }

            $helptext .= "param1 param2 ... paramN" . PHP_EOL;
        }

        $helptext .= PHP_EOL;
    }

    private static function showMainCommands(string &$helptext, ?CommandContext $ctx) : void 
    {
        $helptext .= "# Available commands" . PHP_EOL;

        // Print all commands
        $commands = $ctx->cmdRegistry->getCommands();
        foreach ($commands as $name => $command) {
            $commandTree = self::getCommandTreeString($command);

            // Print command with subcommand tree
            
            $cmdline = "* !{$command->getName()}";

            // Pad command names to align command descriptions
            $cmdline = sprintf("%-20s", $cmdline);

            // Add command description
            $description = $command->getDescription();
            if (!empty($description)) {
                $cmdline .= " - {$description}";
            }

            $helptext .= $cmdline . "\n";
        }
    }

    private static function getCommandTreeString(CommandEntry $command): string
    {
        $str = "";
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
