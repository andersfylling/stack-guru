<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use \StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

class NZT extends AbstractService
{
	private const EVENT = \StackGuru\Core\BotEvent::MESSAGE_ALL_E_COMMAND;


    protected static $name = "nzt"; // Name of the service.
    protected static $description = "Responds to messages that contains the word nzt"; // Short summary of the service purpose.

	private $index;



	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function start(?CommandContext $ctx): bool 
	{
		// add listener to bot..
        $this->index = $ctx->bot->state(NZT::EVENT,  [$this, "response"]);

		return true;
	}

	final public function stop(?CommandContext $ctx): bool 
	{
		// add listener to bot..
        $ctx->bot->removeStateCallable(NZT::EVENT,  $this->index);
        $this->index = null;

		return true;
	}

	final public function running(): bool 
	{
		return null !== $this->index; // no index, means no listening to messages.......
	}

	final public function response(\Discord\Parts\Channel\Message $message, string $event)
	{
		if (strpos(strtolower($message->content), "nzt") !== false) {
	        $rudeNZTResponses = [
	            "Really.. NZT!?",
	            "NZT? Who do you think you are!?",
	            "NZT? Shut up human!",
	            "NZT!? You silly little creature.",
	            "NZT? Blæ?",
	            "How about no more NZT you humans"
	        ];

	        $response = $rudeNZTResponses[array_rand($rudeNZTResponses, 1)];
	        Utils\Response::sendResponse($response, $message);
	    }
	}
}