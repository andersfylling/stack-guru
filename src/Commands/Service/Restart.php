<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Restart extends AbstractCommand
{
    protected static $name = "restart";
    protected static $description = "something about the shutdown command";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $deferred = new Deferred();
        $deferred->reject("Hardcoded to not run due to issues. Commands needs to use promise before this can be implemented.");

        return $deferred->promise();


        if ("" === trim($query)) {

            return "Please specify a service to restart.";
        }


        $serviceEntry = $ctx->services->get($query);

        if (null === $serviceEntry) {
            return "The service `{$query}` was not found.";
        }

    	// Run stop command
    	$this->runCommand("service stop", $query, $ctx, function () use ($query, $ctx, $serviceEntry) {


            // sleep until service has been stopped
            // or max waiting time has been reached.
            $limit = 10; 
            $counter = 0;
            while ($serviceEntry->running() && $counter <= $limit) {
                usleep(500000); // 0.5 seconds
                $counter++;
            }

            if ($counter > $limit) {
                Response::sendMessage("Something went wrong, please check status.", $ctx->message);
                return;
            }

	    	// Run start command
	    	$this->runCommand("service start", $query, $ctx, function () use ($query, $ctx, $serviceEntry)  {
		    	// Run status command
		    	$this->runCommand("service status", $query, $ctx);
	    	});
    	});

    	return "";
    }

    public function runCommand(string $command, string $query, CommandContext $ctx, Callable $callback = null) : void 
    {
    	// get command
    	$cmd = $ctx->cmdRegistry->parseCommandQuery($command)["command"]; // TODO: use interal API, not this...

    	// Should never fire.
    	if (null === $cmd) {
    		Response::sendResponse("Command does not exist `{$command}`", $ctx->message);
    		return;
    	}

    	// Run.
    	$instance = $cmd->createInstance();
    	$response = $instance->process($query, $ctx);

    	// Send update to user.
        if ("" !== $response) {
            Response::sendMessage($response, $ctx->message, $callback);
        }
        else if (null !== $callback) {
            call_user_func_array($callback, []);
        }
        else {
            // done..
        }
    }
}
