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
    protected static $description = "Determine what roles can use which commands";


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
        $counterRoles = 0;
        $counterCommands = 0;
        foreach ($roles as $role) {
            //incase of multiple roles, reset the command counter..
            $counterCommands = 0;

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
            if ($add) {
                if ($family) {

                    $children = []; // BUGGY.... if subcommands can have sub commands.. which they kinda should.
                    if (0 == sizeof($command->getChildren())) {
                        $children = $command->getParent()->getChildren();


                        // also add access to the parent command..
                        if ($ctx->bot->addCommandRole($command->getParent()->getFullName(), $roleid)) {
                            $counterCommands++;
                        }

                        foreach ($children as $childName => $child) {
                            if ("*RECURSION*" == $child) {
                                $children[$childName] = $command; // remove *RECURSION* value..
                                break; // there will only be one.
                            }
                        }
                    }
                    else {
                        $children = $command->getChildren();
                    }

                    foreach ($children as $cmdName => $cmd) {
                        if ("*RECURSION*" == $cmd || null == $cmd) {
                            continue;
                        }

                        if ($ctx->bot->addCommandRole($cmd->getFullName(), $roleid)) {
                            $counterCommands++;
                        }
                    }
                    $counterRoles++;
                }

                // also add access to the default command..
                else if ($ctx->bot->addCommandRole($command->getFullName(), $roleid)) {
                    $counterRoles++;
                }

            }
        }

    	return "Gave {$counterRoles} roles access to {$counterCommands} commands.";
    }
}
