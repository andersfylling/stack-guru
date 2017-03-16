<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class AddRole extends AbstractCommand
{
    protected static $name = "addrole";
    protected static $description = "gives all users a mentionable role";
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

        if (!isset($ctx->guild->roles[$roleid])) {
            return "Role given does not exist in this guild. Talk to bot engineers..";
        }

        $error = "";

        $role = $ctx->guild->roles[$roleid];
        foreach($ctx->guild->members as $member) {
            $member->addRole($role);
            $ctx->guild->members->save($member)->then(function ($response) {}, function ($e) {
                if ("" === $error) {
                    $error = $e->getMessage();
                }
            });

            if ("" != $error) break;
        }


        return "Successfully added role `{$role->name}` to every user. Error: {$error}";
    }
}
