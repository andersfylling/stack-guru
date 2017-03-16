<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use StackGuru\Core\Command\CommandContext as CommandContext;
use StackGuru\Core\Utils;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

class NZT extends AbstractService
{
    protected static $name = "nzt"; // Name of the service.
    protected static $description = "Responds to messages that contains the word nzt"; // Short summary of the service purpose.
    protected static $event = \StackGuru\Core\BotEvent::MESSAGE_ALL_E_COMMAND;


	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function response(string $event, string $msgId, ?Message $message = null, CommandContext $serviceCtx)
	{
		if (null !== $message && strpos(strtolower($message->content), "nzt") !== false) {
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