<?php

namespace StackGuru\Commands\Server;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Server extends AbstractCommand
{
    protected static $name = "server";
    protected static $description = "Make the bot do stuff";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }
}
