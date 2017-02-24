<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Start extends AbstractCommand
{
    protected static $name = "start";
    protected static $description = "something about the shutdown command";


    public function process(string $query, ?CommandContext $ctx): string
    {
    	// Check if service exists in folder.
    	//
    	$serviceEntry = $ctx->services->get($query);

    	if (null === $serviceEntry) {
    		return "The service `{$query}` was not found.";
    	}

    	// Check if service is already running.
    	// 
		$title = $serviceEntry->getName();
    	if ($serviceEntry->running()) {
    		return "The service `{$title}` is already running. Run `!service status {$title}` for more.";
    	}

    	// Tell the user that the service is being enabled.
    	Utils\Response::sendMessage("Starting...", $ctx->message, function () use ($ctx, $serviceEntry, $title) {
	    	$service = $serviceEntry->getInstance();
	    	$success = $service->start($ctx);

    		$response = "";
    		if (true === $success) {
    			$response = "Successfully started service `{$title}`";
    		}
    		else {
    			$response = "ERROR: Was not able to start service `{$title}`";
    			// TODO, store error reason in database or something..
    		}

    		Utils\Response::sendMessage($response, $ctx->message);

    		//return "";
    	});
        


        return "";
    }
}
