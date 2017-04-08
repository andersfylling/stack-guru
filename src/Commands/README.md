# When are commands called
A command gets called whenever a message in a channel (not pm) starts with `!` or mentions the bot by name.

# Writing messages to the terminal for debugging
Whenever you want to write to the terminal, use the `Logger.php` class in `/src/Core/Utils/`. This will in the future write these messages to the database so you can extract errors on the fly from the bot in stead of having to ssh into your server and read the hournal file.

However, if you do not want the messages to be logged, as in a quick debugging ride, just simply use echo or print in stead.

# Sending responses
To send responses, which is a good way of providing feedback after a command has been run, you utilize the `Response.php` file in `/src/Core/Utils/`.
You can also use the DiscordPHP way which is `$ctx->message->channel->sendMessage("string here");`, but the Response class is scripted to work with unit testing and development more easily, so this is the recommended way of doing it.

# Creating a new command
This is the basic template which contains everything needed in order have a command:

Without a response:
```php
<?php
declare(strict_types=1);

namespace StackGuru\Commands\CommandName;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class CommandName extends AbstractCommand
{
    protected static $name = "commandName";
    protected static $description = "Description as to how it works";

    public function process(string $query, CommandContext $ctx): Promise
    {
    	// This allows other processes when a command has been completed.
    	$deferred = new Deferred();

    	// echo something to the terminal and handle the deferred as a success
    	echo "123";
    	$deferred->resolve(); // a string argument can be added.

    	return $deferred->promise();
    }
}
```

With a response:
```php
<?php
declare(strict_types=1);

namespace StackGuru\Commands\CommandName;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class CommandName extends AbstractCommand
{
    protected static $name = "commandName";
    protected static $description = "Description as to how it works";

    public function process(string $query, CommandContext $ctx): Promise
    {
    	// since the Response class returns a promise, we can just return that.
    	return Response::sendMessage("hello there", $ctx->message);
    }
}
```

# Interacting with a service
From the `$ctx` variable you can access a member called `services` which is the class found in `src/Core/Service/Services.php`. It gives you a basic interface to get services and interact with them. Every service inherits the properties and methods from `AbstractService.php` which resides within the same folder. This gives each service basic interactions such as: `stop`, `restart`, `start`, `enable`, `disable`, `running`, `status`. 

Heres a few examples how to interact:
Starting a service:
```php
<?php
declare(strict_types=1);

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Start extends AbstractCommand
{
    protected static $name = "start";
    protected static $description = "Starts a service";


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
    	if ($serviceEntry->running($ctx)) {
            $deferred->reject("The service `{$title}` is already running. Run `!service status {$title}` for more.");
            return $deferred->promise();
    	}

    	// Tell the user that the service is being started.
    	$this->reply("Starting...", $ctx, false, false, function () use ($ctx, $serviceEntry, $title, $deferred) {
            // set service instance.
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
    		}

            $this->reply($response, $ctx, false, false, function() use($deferred) {
                $deferred->resolve();
            });
    	});
        

        return $deferred->promise();
    }
}
```