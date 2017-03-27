<?php
declare(strict_types=1);

namespace StackGuru\Services;
use \StackGuru\Core\Service\AbstractService;
use StackGuru\Core\Command\CommandContext as CommandContext;
use StackGuru\Core\Utils;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

class AddMemberRole extends AbstractService
{
    protected static $name = "addmemberrole"; // Name of the service.
    protected static $description = "Gives members that have just joined the guild the member role"; // Short summary of the service purpose.
    protected static $event = \StackGuru\Core\BotEvent::MEMBER_JOINED_GUILD;


	final public function process(string $query, ?CommandContext $ctx): string
	{
		return "";
	}

	// this should query the database to see what default roles are.
	final public function response(string $event, string $msgId, CommandContext $serviceCtx, $member = null)
	{
		if (null === $member) {
			return;
		}

		if (!isset($member->guild->roles["280835202299985932"])) {
            return;
        }

        $role = $member->guild->roles["280835202299985932"];
        $member->addRole($role);
        $member->guild->members->save($member);
	}
}