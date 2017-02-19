<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class RemoveRole extends AbstractCommand
{
    protected static $name = "removerole";
    protected static $description = "Removes a role from all users.";
    protected static $default = ""; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
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

        if (!isset($ctx->bot->guild->roles[$roleid])) {
            return "Role given does not exist in this guild. Talk to bot engineers..";
        }

        $role = $ctx->bot->guild->roles[$roleid];
        foreach($ctx->bot->guild->members as $member) {
            $member->removeRole($role);
            $ctx->bot->guild->members->save($member);
        } 


        return "Successfully removed role `{$role->name}` from every user.";
    }
}
