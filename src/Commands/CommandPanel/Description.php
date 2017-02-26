<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


// # Edit description for command
// !commandpanel description <command> <description text goes here>
class Description extends AbstractCommand
{
    protected static $name = "description";
    protected static $description = "Stops a service";


    public function process(string $query, ?CommandContext $ctx): string
    {
        $words = explode(' ', $query);
        if (1 >= sizeof($words)) {
            return "No description given.";
        }

        // check if command exists
        // 
        $cmdArr = $ctx->cmdRegistry->parseCommandQuery($query);
        if (null === $cmdArr["command"]) {
            return "No valid commands found in that query.";
        }

        $command        = $cmdArr["command"];
        $description    = $cmdArr["query"];

        // Store to database first, if that works, then update cmdRegistry
        $success = $ctx->bot->updateCommandDescription($command->getFullName(), $description);
        if (!$success) {
            return "ERROR: Unable to update database description. Nothing else updated.";
        }

        $command->setDescription($description);


        return "Description for `{$command->getName()}` was set to `{$description}`.";
    }
}
