<?php
declare(strict_types=1);

namespace StackGuru\Commands\Help;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Command\CommandEntry;
use StackGuru\Core\Utils\StringParser;


class Help extends AbstractCommand
{
    protected static $name = "help";
    protected static $description = "returns a list of available bot commands";

    private static $printf1 = "%-28s";
    private static $printf2 = "%-10s";


    public function process(string $query, ?CommandContext $ctx): string
    {
        // Encapsulate command list in code block
        $helptext = "```markdown" . PHP_EOL;

        // Decide if all commands should be shown, just one command or a subcommand.
        // also mention how a service is used.
        
        //remove --help and -h
        $query = str_replace("--help", "", $query);
        $query = str_replace("-h", "", $query);

        $keys = StringParser::getFirstWords($query, 2);

        // a sub command
        if (isset($keys[1]) && $this->isCommand($ctx, $keys[0], $keys[1])) {
            $command = $this->getCommand($ctx, $keys[0]);
            $subcommand = $this->getCommand($ctx, $keys[0], $keys[1]);
            $alias = $subcommand->getInfoAliases();
            $aliasSize = sizeof($alias);


            // Command name
            $helptext .= "# Command name" . PHP_EOL;
            $helptext .= "* {$command->getName()} {$subcommand->getName()}" . PHP_EOL . PHP_EOL;

            // Aliases
            $helptext .= "# Command alias [{$aliasSize}]" . PHP_EOL;
            foreach($alias as $a) {
                $helptext .= "* {$a}" . PHP_EOL;
            }
            $helptext .= PHP_EOL;

            // Usage example
            self::showCommandUsage($helptext, [$command->getName() . ' ' . $subcommand->getName()]);

            // Description
            $helptext .= "# Description" . PHP_EOL;
            $helptext .= "* {$subcommand->getInfoDescription()}" . PHP_EOL . PHP_EOL;

            // Sub commands
            self::showMainCommands($helptext, $ctx, $subcommand->getChildren(), $command);
        } 

        // A main command
        else if (isset($keys[0]) && $this->isCommand($ctx, $keys[0])) {
            $command = $this->getCommand($ctx, $keys[0]);
            $alias = $command->getInfoAliases();
            $aliasSize = sizeof($alias);


            $helptext .= "# Command name" . PHP_EOL;
            $helptext .= "* {$command->getName()}" . PHP_EOL . PHP_EOL;

            // Aliases
            $helptext .= "# Command alias [{$aliasSize}]" . PHP_EOL;
            foreach($alias as $a) {
                $helptext .= "* {$a}" . PHP_EOL;
            }
            $helptext .= PHP_EOL;

            self::showCommandUsage($helptext, [$command->getName()]);

            $helptext .= "# Description" . PHP_EOL;
            $helptext .= "* {$command->getInfoDescription()}" . PHP_EOL . PHP_EOL;


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

    private function getCommand(CommandContext $ctx, string $n1, string $n2 = null): ?CommandEntry 
    {
        if (null === $n2) {
            return $ctx->cmdRegistry->getCommand($n1);
        }
        else {
            return $ctx->cmdRegistry->getSubCommand($n1, $n2);
        }
    }

    private function isCommand(CommandContext $ctx, string $n1, string $n2 = null): bool 
    {
        return null !== $this->getCommand($ctx, $n1, $n2);
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

    private static function showMainCommands(string &$helptext, CommandContext $ctx, $commands, $parentCommand = null) : void 
    {
        $nr = sizeof($commands);

        $title = "";
        $title .= sprintf(self::$printf1, "# Available commands [{$nr}]");

        if (0 !== $nr) {
            $title .= sprintf(self::$printf2, "Enabled");
            $title .= "Description";
        }
        
        $helptext .= $title . PHP_EOL;

        // Print all commands
        foreach ($commands as $name => $command) {
            $ctx->commandEntry = $command;
            if (!$command->hasPermission($ctx)) {
                continue;
            }

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
            $description = $command->getInfoDescription();
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
