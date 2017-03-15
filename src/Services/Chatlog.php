<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use StackGuru\Core\Command\CommandContext as CommandContext;
use StackGuru\Core\Utils;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

class Chatlog extends AbstractService
{
    protected static $name = "chatlog"; // Name of the service.
    protected static $description = "stores messages to database"; // Short summary of the service purpose.
    protected static $event = \StackGuru\Core\BotEvent::MESSAGE_ALL_I_SELF;


	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function response(string $event, string $msgId, ?Message $message = null, CommandContext $serviceCtx)
	{

		// ignore private messaging
		if ($message !== null && $message->channel->is_private) {
			return;
		}

		// If this channel can't be logged, ignore it.
		if ($message !== null && !$serviceCtx->database->chatlog_loggableChannel($message->channel_id)) {
			return;
		}


		// new message
		if ($message !== null && DiscordEvent::MESSAGE_CREATE == $event) {
			if ($serviceCtx->database->chatlog_saveMessage($msgId, $message->channel_id, $message->author->id)) {
				$serviceCtx->database->chatlog_saveMessageContent($message->content, $message->id);
			}
		}

		// updated message
		else if ($message !== null && DiscordEvent::MESSAGE_UPDATE == $event) {
			$serviceCtx->database->chatlog_saveMessageContent($message->content, $message->id);
		}

		// deleted message
		else if ($message === null && DiscordEvent::MESSAGE_DELETE == $event) {
			$deleted = true;
			$serviceCtx->database->chatlog_updateMessage($msgId, $deleted);
		}
	}
}