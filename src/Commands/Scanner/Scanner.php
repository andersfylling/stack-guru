<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Scanner extends AbstractCommand
{
    protected static $name = "scanner";
    protected static $description = "find drug information";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
    	$users = $this->getUsers($ctx);
        $roles = $this->getRoles($ctx);
        var_dump($roles);
    	$list = "";
    	foreach($ctx->message->channel->guild->members as $key => $value) {
		    $list .= $value->username . PHP_EOL;
		    //var_dump($value);
		}

        return $list;
    }

    public function getUsers(?CommandContext $ctx)
    {
        $users = $ctx->message->channel->guild->members;
        $ret = [];

        // For some reasons there might be duplicates here... thank discordphp team.
        //foreach()
        //
        var_dump($users);

        return $users;
    }

    public function getRoles(?CommandContext $ctx)
    {
        return $ctx->discord->roles;
    }
}
