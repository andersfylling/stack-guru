<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Stop extends AbstractCommand
{
    protected static $name = "stop";
    protected static $description = "Stops a service";

    public function process(string $query, CommandContext $ctx): Promise
    {
        $deferred = new Deferred();
    	// Check if service exists in folder.
    	//
    	$serviceEntry = $ctx->services->get($query);

        if (null === $serviceEntry) {
            $deferred->reject("The service `{$query}` was not found.");
            return $deferred->promise();
        }

    	// Check if service is already running.
    	// 
		$title = $serviceEntry->getName();
    	if (!$serviceEntry->running($ctx)) {
            $deferred->reject("The service `{$title}` is not running. Run `!service status {$title}` for more.");
            return $deferred->promise();
    	}

    	// Tell the user that the service is being enabled.
    	$this->reply("Stopping...", $ctx, false, false, function () use ($ctx, $serviceEntry, $title, $deferred) {
	    	// set service instance, in case it has been stopped or some error happened.
            if (null === $serviceEntry->getInstance()) {
                $serviceEntry->createInstance();
            }

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

            $this->reply($response, $ctx, false, false, function() use($deferred) {
                $deferred->resolve();
            });
    	});
        

        return $deferred->promise();
    }
}
