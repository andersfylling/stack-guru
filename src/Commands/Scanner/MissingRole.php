<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;
use StackGuru\Core\Utils\Response as Response;


class MissingRole extends AbstractCommand
{
    protected static $name = "missingrole";
    protected static $description = "List all users that are missing a role";
    protected static $default = ""; // default sub-command

    protected $progress = 0;
    protected $totalNumberOfMembers = 0;
    protected $currentChannel = null;


    public function process(string $query, CommandContext $ctx): Promise
    {
        $deferred = new Deferred();

        if (!(strpos($query, "<@&") !== false)) {
            // might not have been a role mention, what if its just the roleid
            $roleid = trim($query);
            if (!isset($ctx->guild->roles[$roleid])) {
                return Response::sendMessage("Did you mention a role?", $ctx->message);
            }
        }

        $output = null;
        preg_match('~<@&(.*?)>~', $query, $output);

        if (2 != sizeof($output)) {
            $res = "Did you mention more than one role? Try to `!scanner giveusersrole @random` where random is a legit role.";
            return Response::sendMessage($res, $ctx->message);
        }


        $roleid = $output[1];

        if (!isset($ctx->guild->roles[$roleid])) {
            $res = "Role given does not exist in this guild. Talk to bot engineers..";
            return Response::sendMessage($res, $ctx->message);
        }

        $role = $ctx->guild->roles[$roleid];
        $res = "";
        foreach($ctx->guild->members as $member) {
            if (!isset($member->roles[$roleid])) {
                $res .= "<@{$member->user->id}>" . PHP_EOL;
            }

            if (sizeof($res) > 1900) {
                Response::sendMessage($res, $ctx->message);
                $res = "";
            }
        }
        Response::sendMessage($res, $ctx->message);
        $deferred->resolve();

        return $deferred->promise();
    }
}
