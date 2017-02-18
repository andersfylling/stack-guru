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
        return $ctx->discord->users;
    }

    public function getRoles(?CommandContext $ctx)
    {
        return $ctx->discord->roles;
    }
}
