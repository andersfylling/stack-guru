<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;
use StackGuru\Core\Utils\Response as Response;


class Scanner extends AbstractCommand
{
    protected static $name = "scanner";
    protected static $description = "Scan guild for all users";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }

    public function getUsers(?CommandContext $ctx)
    {
        $users = $ctx->guild->members;
        $ret = [];

        // For some reasons there might be duplicates here... thank discordphp team.
        //foreach()
        //
        
        return $users;
    }

    public function getRoles(?CommandContext $ctx)
    {
        return $ctx->guild->roles;
    }

    public function getChannels(?CommandContext $ctx)
    {
        //var_dump($ctx->guild);
        return $ctx->guild->channels;
    }
}
