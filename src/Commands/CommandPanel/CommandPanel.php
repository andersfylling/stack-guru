<?php

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class CommandPanel extends AbstractCommand
{
    protected static $name = "commandpanel";
    protected static $description = "bot service commands";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }
}
