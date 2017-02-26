<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Save extends AbstractCommand
{
    protected static $name = "save";
    protected static $description = "Save scanned users";
    protected static $default = ""; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        $users = $ctx->parentCommand->getUsers($ctx);
        $roles = $ctx->parentCommand->getRoles($ctx);


        // the db command should return true when stored, so bot can respond with legit numbers.
        foreach($users as $user) {
            $ctx->bot->saveUser($user);
        }

        foreach ($roles as $role) {
            $ctx->bot->saveRole($role);
        }


        $res = "";
        $res .= "```Markdown" . PHP_EOL;
        $res .= "# Log" . PHP_EOL;
        $res .= "* Members: " . sizeof($users) . PHP_EOL;
        $res .= "* Roles:   " . sizeof($roles) . PHP_EOL;
        $res .= "```";


        return $res;
    }
}
