<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class UserCount extends AbstractCommand
{
    protected static $name = "UserCount";
    protected static $description = "Display number of members";


    public function process(string $query, ?CommandContext $ctx): string
    {
        $users = $ctx->parentCommand->getUsers($ctx);
        $usercount = sizeof($users);


        $res = "";
        $res .= "```Markdown" . PHP_EOL;
        $res .= "# Members [{$usercount}]" . PHP_EOL;
        $res .= "```";


        return $res;
    }
}
