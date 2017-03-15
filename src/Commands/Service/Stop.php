<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Stop extends AbstractCommand
{
    protected static $name = "stop";
    protected static $description = "Stops a service";


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
    	if (!$serviceEntry->running()) {
    		return "The service `{$title}` is not running. Run `!service status {$title}` for more.";
    	}

    	// Tell the user that the service is being enabled.
    	Utils\Response::sendMessage("Stopping...", $ctx->message, function () use ($ctx, $serviceEntry, $title) {
	    	$service = $serviceEntry->getInstance();
	    	$success = $service->stop($ctx);

    		$response = "";
    		if (true === $success) {
    			$response = "Successfully stopped service `{$title}`";
    		}
    		else {
    			$response = "ERROR: Was not able to stop service `{$title}`";
    			// TODO, store error reason in database or something..
    		}

    		Utils\Response::sendMessage($response, $ctx->message);

    		//return "";
    	});
        


        return "";
    }
}
