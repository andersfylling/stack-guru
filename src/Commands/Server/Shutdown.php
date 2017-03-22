<?php

namespace StackGuru\Commands\Server;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Shutdown extends AbstractCommand
{
    protected static $name = "shutdown";
    protected static $description = "something about the shutdown command";


    public function process(string $query, CommandContext $ctx): Promise
    {
    	exit(1); //stops the bot with exit code 1
        $response = "Exited.."; // this will never be reached
        return Response::sendMessage($response, $ctx->message);
    }
}
