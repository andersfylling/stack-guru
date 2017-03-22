<?php

namespace StackGuru\Commands\Drug;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Drug extends AbstractCommand
{
    protected static $name = "drug";
    protected static $description = "find drug information";
    protected static $default = "info"; // default sub-command


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }
}
