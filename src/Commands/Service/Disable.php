<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;

/**
 * Remove a service from the database entry.
 */
class Disable extends AbstractCommand
{
    protected static $name = "disable";
    protected static $description = "Disable a service so it won't run at boot";


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
    	$title = $serviceEntry->getName();

    	// Tell the user that the service is being enabled.
    	$this->reply("Disabling...", $ctx, false, false, function () use ($ctx, $serviceEntry, $title, $deferred) {

    		if (!$serviceEntry->isEnabled($ctx)) {
    			$response = "The service {$title} is has not been enabled yet.";
                $this->reply($response, $ctx, false, false, function() use($deferred) {
                    $deferred->resolve();
                });
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

            $this->reply($response, $ctx, false, false, function() use($deferred) {
                $deferred->resolve();
            });
    	});
        

        return $deferred->promise();
    }
}
