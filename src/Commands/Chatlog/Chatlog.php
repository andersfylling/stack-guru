<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Chatlog extends AbstractCommand
{
    protected static $name = "chatlog";
    protected static $description = "Logs all chat messages for selected channels";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }

    public function getUsers(CommandContext $ctx)
    {
        $users = $ctx->guild->members;
        $ret = [];

        // For some reasons there might be duplicates here... thank discordphp team.
        //foreach()
        //
        
        return $users;
    }

    public function getRoles(CommandContext $ctx)
    {
        return $ctx->discord->roles;
    }
}
