<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;
use StackGuru\Core\Utils\Response as Response;


class RemoveRole extends AbstractCommand
{
    protected static $name = "removerole";
    protected static $description = "Removes a role from all users.";
    protected static $default = ""; // default sub-command


    public function process(string $query, CommandContext $ctx): Promise
    {
        if (!(strpos($query, "<@&") !== false)) {
            return "Did you mention a role?";
        }
        var_dump($query);

        $output = null;
        preg_match('~<@&(.*?)>~', $query, $output);

        if (2 != sizeof($output)) {
            return "Did you mention more than one role? Try to `!scanner giveusersrole @random` where random is a legit role.";
        }


        $roleid = $output[1];

        if (!isset($ctx->guild->roles[$roleid])) {
            return "Role given does not exist in this guild. Talk to bot engineers..";
        }

        $role = $ctx->guild->roles[$roleid];
        foreach($ctx->guild->members as $member) {
            $member->removeRole($role);
            $ctx->guild->members->save($member);
        } 

        $res = "Successfully removed role `{$role->name}` from every user.";
        return Response::sendMessage($res, $ctx->message);
    }
}
