<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use StackGuru\Core\Command\CommandContext as CommandContext;
use StackGuru\Core\Utils;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

class GreetNewMembers extends AbstractService
{
    protected static $name = "greetnewmembers"; // Name of the service.
    protected static $description = "Greets members that have just joined the guild"; // Short summary of the service purpose.
    protected static $event = \StackGuru\Core\BotEvent::MEMBER_JOINED_GUILD;


	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	final public function response(string $e, string $msgId, CommandContext $serviceCtx, $member = null)
	{
		if (null === $member) {
			return;
		}

		$member->user->sendMessage("1");

		// if a general chat exist we greet the user in there.. for now.
		$chan = null;
		foreach ($member->guild->channels as $channel) {
			if ("general" == strtolower($channel->name)) {
				$chan = $channel;
				break;
			}
		}

		$channels = "";
		foreach ($member->guild->channels as $channel) {
			$channels .= $channel->name . PHP_EOL;
		}

		$member->user->sendMessage($channels);


		if (null === $chan) {
			if (isset($member->guild->channels["239926482674253825"])) {
				$chan = $member->guild->channels["239926482674253825"];
			}
			else {
				return;
			}
		}

		// see if a rules or rule channel exists, and tell the user to check it out.
		$rules = null;
		foreach ($member->guild->channels as $channel) {
			if ("rules" == $channel["name"] || "rule" == $channel["name"]) {
				$rules = $channel;
				break;
			}
		}

		$welcome = "";

		// add user welcome message
		$userid = $member->id;
		$welcome .= "Welcome to the server <@{$userid}>";

		//add rules channel
		if (null !== $rules) {
			$rulesid = $rules->id;
			$welcome .= ", please see <#{$rulesid}>";
		}

		$chan->sendMessage($welcome . " and enjoy your stay!");
	}
}