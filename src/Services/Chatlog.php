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
		if ($message->channel->is_private) {
			return;
		}

		// If this channel can't be logged, ignore it.
		if (!$serviceCtx->bot->chatlog_loggableChannel($message->channel_id)) {
			return;
		}


		// new message
		if (DiscordEvent::MESSAGE_CREATE == $event) {
			if ($serviceCtx->bot->chatlog_saveMessage($msgId, $message->channel_id, $message->author->id)) {
				$serviceCtx->bot->chatlog_saveMessageContent($message->content, $message->id);
			}
		}

		// updated message
		else if (DiscordEvent::MESSAGE_UPDATE == $event) {
			// chatlog_updateMessageContent(...)
		}

		// deleted message
		else if (DiscordEvent::MESSAGE_DELETE == $event) {
			// chatlog_updateMessageContent(...)
		}
	}
}