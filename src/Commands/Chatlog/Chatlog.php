<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\BotEvent;


class Chatlog extends AbstractCommand
{
    protected static $name = "chatlog";
    protected static $description = "Logs all chat messages for selected channels";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        //var_dump($ctx->bot->guild->channels);
        //
        //
        
        $ctx->bot->state(BotEvent::MESSAGE_ALL_I_SELF,  [$this, "log"]);


    	return "hellu";
    }

    public function log(\Discord\Parts\Channel\Message $message, string $event) 
    {
        echo "---LOGGER", PHP_EOL, "$event: $message->content", PHP_EOL;
    }

    public function getUsers(?CommandContext $ctx)
    {
        $users = $ctx->message->channel->guild->members;
        $ret = [];

        // For some reasons there might be duplicates here... thank discordphp team.
        //foreach()
        //
        
        return $users;
    }

    public function getRoles(?CommandContext $ctx)
    {
        return $ctx->discord->roles;
    }
}
