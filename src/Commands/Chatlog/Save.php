<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Save extends AbstractCommand
{
    protected static $name = "save";
    protected static $description = "save something users";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        $users = $ctx->parentCommand->getUsers($ctx);

        foreach($users as $user) {
            $ctx->bot->saveUser($user);
        }

        return " " . sizeof($users) . " users saved.";
    }
}
