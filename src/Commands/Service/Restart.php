<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Restart extends AbstractCommand
{
    protected static $name = "restart";
    protected static $description = "something about the shutdown command";


    public function process(string $query, ?CommandContext $ctx): string
    {
    	// Run stop command
    	$this->runCommand("service stop", $ctx, function () use ($ctx) {
	    	// Run start command
	    	$this->runCommand("service start", $ctx, function () use ($ctx)  {
		    	// Run status command
		    	$this->runCommand("service status", $ctx);
	    	});
    	});

    	return "";
    }

    public function runCommand(string $command, ?CommandContext $ctx, ?\Closure $callback = null) : void 
    {
    	// get command
    	$cmd = $ctx->cmdRegistry->parseCommandQuery($command)["command"]; // TODO: use interal API, not this...

    	// Should never fire.
    	if (null === $cmd) {
    		Utils\Response::sendResponse("Command does not exist `{$command}`", $ctx->message);
    		return;
    	}

    	// Run.
    	$instance = $cmd->createInstance();
    	$response = $instance->process("", $ctx);

    	// Send update to user.
    	Utils\Response::sendMessage($response, $ctx->message, $callback);
    }
}
