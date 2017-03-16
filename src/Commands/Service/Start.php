<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Start extends AbstractCommand
{
    protected static $name = "start";
    protected static $description = "Starts a service";


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
    	if ($serviceEntry->running($ctx)) {
    		return "The service `{$title}` is already running. Run `!service status {$title}` for more.";
    	}

    	// Tell the user that the service is being enabled.
    	Utils\Response::sendMessage("Starting...", $ctx->message, function () use ($ctx, $serviceEntry, $title) {


            // set service instance, in case it has been stopped or some error happened.
            if (null === $serviceEntry->getInstance()) {
                $serviceEntry->createInstance();
            }

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
