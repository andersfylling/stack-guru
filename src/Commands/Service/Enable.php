<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

/**
 * Adds a service to the database entry so that it can be started, stopped, restarted and whatever.
 */
class Enable extends AbstractCommand
{
    protected static $name = "enable";
    protected static $description = "Enable a service to automatically run at boot";


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
    		return "The service `{$title}` is currently running. Run `!service status {$title}` for more.";
    	}

    	// Tell the user that the service is being enabled.
    	Utils\Response::sendMessage("Enabling...", $ctx->message, function () use ($ctx, $serviceEntry, $title) {

    		if ($serviceEntry->isEnabled($ctx)) {
    			$response = "The service {$title} is already enabled. You can start it by typing `!service start {$title}`";
    			Utils\Response::sendMessage($response, $ctx->message);
    			return;
    		}


	    	// set service instance
	    	if (null === $serviceEntry->getInstance()) {
	    		$serviceEntry->createInstance();
	    	}

	    	$service = $serviceEntry->getInstance();
	    	$success = $service->enable($ctx);

    		$response = "";
    		if (true === $success) {
    			$response = "Successfully enabled service `{$title}`";
    		}
    		else {
    			$response = "ERROR: Was not able to enable service `{$title}`";
    			// TODO, store error reason in database or something..
    		}

    		Utils\Response::sendMessage($response, $ctx->message);

    		//return "";
    	});
        


        return "";
    }
}
