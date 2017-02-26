<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

// # Add a role that can use the command
// !commandpanel role add <command> @role
// 
// # Add a role for a command and all its children
// !commandpanel role family add <command> @role
// 
// # Remove a role for a command and all its children
// !commandpanel role family remove <command> @role
// 
// # Remove a role from the commands whitelist
// !commandpanel role remove <command> @role
class Role extends AbstractCommand
{
    protected static $name = "role";
    protected static $description = "something about the shutdown command";


    public function process(string $query, ?CommandContext $ctx): string
    {
        $add = false;
        $remove = false;
        $family = false;
        $command = null;
        $roleid = null;

        $words = explode(' ', $query);

        // checks
        if ("family" === $words[0]) {
            $family = true;
            unset($words[0]);

            $words = array_values($words);
        }

        if ("add" === $words[0]) {
            $add = true;
            unset($words[0]);

            $words = array_values($words);
        }
        else if ("remove" === $words[0]) {
            $remove = true;
            unset($words[0]);

            $words = array_values($words);
        }

        if (!$add && !$remove) {
            return "You didn't specify an action [family, add, remove]: role <action>[ <action2>] <command>[ <subcommand>] @role";
        }

        // check if command exists
        // 
        $cmdArr = $ctx->cmdRegistry->parseCommandQuery(implode(' ', $words));
        if (null === $cmdArr["command"]) {
            return "No valid commands found in that query.";
        }
        $command = $cmdArr["command"];

        $roles = explode(' ', $cmdArr["query"]); // get all words after the command
        $counter = 0;
        foreach ($roles as $role) {
            //check if role is valid
            $output = [];
            preg_match('~<@&(.*?)>~', $role, $output);

            if (2 != sizeof($output)) {
                continue;
            }

            $roleid = $output[1];

            // if it was not a match, skip
            if ("" === $roleid) {
                continue;
            }

            // if given role does not exist in this guild, skip
            if (!isset($ctx->guild->roles[$roleid])) {
                continue;
            }

            // ok, add role for command to database.
            if ($add && $ctx->bot->addCommandRole($command->getFullName(), $roleid)) {
                $counter++;
            }
        }

    	return "Gave {$counter} access to the command.";
    }
}
