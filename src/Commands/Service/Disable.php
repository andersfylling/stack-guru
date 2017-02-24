<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

/**
 * Remove a service from the database entry.
 */
class Disable extends AbstractCommand
{
    protected static $name = "disable";
    protected static $description = "Disable a service so it won't run at boot";


    public function process(string $query, ?CommandContext $ctx): string
    {
    	// Check if service exists in folder.
    	//
    	$serviceEntry = $ctx->services->get($query);

    	if (null === $serviceEntry) {
    		return "The service `{$query}` was not found.";
    	}
    	$title = $serviceEntry->getName();

    	// Tell the user that the service is being enabled.
    	Utils\Response::sendMessage("Disabling...", $ctx->message, function () use ($ctx, $serviceEntry, $title) {

    		if (!$serviceEntry->isEnabled($ctx)) {
    			$response = "The service {$title} is has not been enabled yet.";
    			Utils\Response::sendMessage($response, $ctx->message);
    			return;
    		}


	    	// set service instance, in case it has been stopped or some error happened.
	    	if (null === $serviceEntry->getInstance()) {
	    		$serviceEntry->createInstance();
	    	}

	    	$service = $serviceEntry->getInstance();
	    	$success = $service->disable($ctx);

    		$response = "";
    		if (true === $success) {
    			$response = "Successfully disabled service `{$title}`";
    		}
    		else {
    			$response = "ERROR: Was not able to disable service `{$title}`";
    			// TODO, store error reason in database or something..
    		}

    		Utils\Response::sendMessage($response, $ctx->message);
    	});
        


        return "";
    }
}
