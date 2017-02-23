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

    private static $printf1 = "%-27s";
    private static $printf2 = "%-10s";


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

            self::showCommandUsage($helptext, [$command->getName() . ' ' . $subcommand->getName()]);

            $helptext .= "# Description" . PHP_EOL;
            $helptext .= "* {$subcommand->getDescription()}" . PHP_EOL . PHP_EOL;

            self::showMainCommands($helptext, $ctx, $subcommand->getChildren(), $command);
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
            self::showMainCommands($helptext, $ctx, $command->getChildren(), $command);
        } 
        

        // default, all commands
        else {
            self::showCommandUsage($helptext, ["command", "subcommand"]);

            self::showMainCommands($helptext, $ctx, $ctx->cmdRegistry->getCommands());
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

    private static function showMainCommands(string &$helptext, ?CommandContext $ctx, $commands, $parentCommand = null) : void 
    {
        $nr = sizeof($commands);

        $ttile = "";
        $title .= sprintf(self::$printf1, "# Available commands [{$nr}]");

        if (0 !== $nr) {
            $title .= sprintf(self::$printf2, "Enabled");
            $title .= "Description";
        }
        
        $helptext .= $title . PHP_EOL;

        // Print all commands
        foreach ($commands as $name => $command) {
            $commandTree = self::getCommandTreeString($command);

            // Print command with subcommand tree
            if (null === $parentCommand) {
                $cmdline = "* !{$command->getName()}";
            }
            else {
                $cmdline = "* !{$parentCommand->getName()} {$command->getName()}";
            }

            // Pad command names to align command descriptions
            $cmdline  = sprintf(self::$printf1, $cmdline);
            $cmdline .= sprintf(self::$printf2, "true");

            // Add command description
            $description = $command->getDescription();
            if (!empty($description)) {
                $cmdline .= "{$description}";
            }

            $helptext .= $cmdline . PHP_EOL;
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
