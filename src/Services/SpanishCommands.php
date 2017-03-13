<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use \StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

class SpanishCommands extends AbstractService
{
    protected static $name = "spanishcommands"; // Name of the service.
    protected static $description = "Responds to messages that contains the spanish exclamation mark ¡"; // Short summary of the service purpose.
    protected static $event = \StackGuru\Core\BotEvent::MESSAGE_ALL_E_SELF;


	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function qualified(string $msg): bool 
	{
		return null !== $msg && '¡' == \StackGuru\Core\Utils\StringParser::getCharAt(0, $msg);
	}

	final public function response(string $event, string $msgId, ?Message $message = null, ?Message $oldMessage = null)
	{
		if (!$this->qualified($message->content)) {
			return;
		}

		// different responses. should be stored into database later..
		$spanishResponse = [
            "No hablo español, lo siento"
        ];

        $response = $spanishResponse[array_rand($spanishResponse, 1)];
        Utils\Response::sendResponse($response, $message);
	}
}