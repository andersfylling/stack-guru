<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use \StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;

class SpanishCommands extends AbstractService
{
	private const EVENT = \StackGuru\Core\BotEvent::MESSAGE_ALL_E_SELF;


    protected static $name = "spanishcommands"; // Name of the service.
    protected static $description = "Responds to messages that contains the spanish exclamation mark ¡"; // Short summary of the service purpose.

	private $index;



	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function start(?CommandContext $ctx): bool 
	{
		// add listener to bot..
        $this->index = $ctx->bot->state(SpanishCommands::EVENT,  [$this, "response"]);

		return true;
	}

	final public function stop(?CommandContext $ctx): bool 
	{
		// remove listener from bot..
        $ctx->bot->removeStateCallable(SpanishCommands::EVENT,  $this->index);
        $this->index = null;

		return true;
	}

	final public function running(): bool 
	{
		return null !== $this->index; // no index, means no listening to messages.......
	}

	final public function qualified(string $msg): bool 
	{
		return null !== $msg && '¡' == \StackGuru\Core\Utils\StringParser::getCharAt(0, $msg);
	}

	final public function response(\Discord\Parts\Channel\Message $message, string $event)
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