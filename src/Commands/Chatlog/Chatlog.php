<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Chatlog extends AbstractCommand
{
    protected static $name = "chatlog";
    protected static $description = "Logs all chat messages for selected channels";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {

    	return "hellu";
    }

    public function getUsers(?CommandContext $ctx)
    {
        $users = $ctx->message->channel->guild->members;
        $ret = [];

        // For some reasons there might be duplicates here... thank discordphp team.
        //foreach()
        //
        
        return $users;
    }

    public function getRoles(?CommandContext $ctx)
    {
        return $ctx->discord->roles;
    }
}
