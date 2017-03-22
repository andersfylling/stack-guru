<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Command\CommandEntry;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


// # Add an alias for a command
// !commandpanel alias add <command> <alias>
// OR
// !commandpanel alias <command> <alias>
// 
// # Remove an alias from a command
// !commandpanel alias remove <command> <alias>
class Alias extends AbstractCommand
{
    protected static $name = "alias";
    protected static $description = "Add an alias for a command. `!cp alias add <command> <alias>`";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $words = explode(' ', $query);
        $success = false;

        if (1 >= sizeof($words)) {
            return "You must give a command and an alias to use. Syntax: <action> <command> <alias>, action=[add,remove].";
        }

        if (3 <= sizeof($words)) {
            $command = $this->getCommandEntry($words[1], $ctx);

            if (null !== $command && "add" === $words[0]) {
                $success = $this->addAlias($command, $words[2], $ctx);
            }
            else if (null !== $command && "remove" === $words[0]) {
                $success = $this->removeAlias($command, $words[2], $ctx);
            }
        }
        else if (2 == sizeof($words)) {
            $command = $this->getCommandEntry($words[0], $ctx);

            if (null !== $command) {
                $success = $this->addAlias($command, $words[1], $ctx);   
            }
        }

        $response = "";
        if ($success) {
            $response = "Alias successfully handled.";
        }
        else {
            $response = "Alias handling encountered an issue. See terminal/log for more.";
        }


        return Response::sendMessage($response, $ctx->message);
    }

    public function getCommandEntry(string $cmd, CommandContext $ctx): ?CommandEntry
    {
        return $ctx->cmdRegistry->getCommand($cmd);
    }

    public function addAlias(CommandEntry $cmd, string $alias, CommandContext $ctx): bool
    {
        $success = false;
        if ($ctx->database->saveCommandAlias($cmd->getFullName(), $alias)) {
            $success = $ctx->cmdRegistry->addCommandAlias($alias, $cmd);
            echo "Alias error: able to add alias to database but not to command entry...", PHP_EOL;
        }

        return $success;
    }

    public function removeAlias(CommandEntry $cmd, string $alias, CommandContext $ctx): bool
    {
    }
}
