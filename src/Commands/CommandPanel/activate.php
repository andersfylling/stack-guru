<?php
declare(strict_types=1);

namespace StackGuru\Commands\CommandPanel;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;

/**
 * Adds a service to the database entry so that it can be started, stopped, restarted and whatever.
 */
class Activate extends AbstractCommand
{
    protected static $name = "activate";
    protected static $description = "Enable a service to automatically run at boot";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $response = "Not implemented yet";
        return Response::sendMessage($response, $ctx->message);
    }
}
