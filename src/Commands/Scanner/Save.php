<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Save extends AbstractCommand
{
    protected static $name = "save";
    protected static $description = "save scanned users";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
    	$d = $ctx->discord;
    	$users = $d->users;
    	$list = "";
    	foreach($ctx->message->channel->guild->members as $key => $value) {
		    $list .= $value->username . PHP_EOL;
		    var_dump($value);
		}

        return $list;
    }
}
