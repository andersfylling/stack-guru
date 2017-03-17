<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;
use React\Promise\Deferred;


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

        $role = $ctx->guild->roles[$roleid];
        // foreach($ctx->guild->members as $member) {
        //     $member->addRole($role);
        //     $ctx->guild->members->save($member)->then(function ($response) {}, function ($e) use ($ctx) {
        //         $ctx->message->channel->sendMessage($e->getMessage());
        //     });
        // }


        $this->addRoleToMembersDeferred($ctx->message->channel->guild, $role)->then(function($r) use($ctx, $role) {
            $ctx->message->channel->sendMessage("Successfully added role `{$role->name}` to every user.");
        });



        return "";
    }

    private function addRoleToMembersDeferred($guild, $role) 
    {
        $deferred = new Deferred();
        $members = $guild->members->all();
        $this->addRoleToMembers($guild, $members, $role, $deferred);

        return $deferred->promise();
    }

    private function addRoleToMembers($guild, $members, $role, $deferred)
    {
        if (empty($members)) {
            return $deferred->resolve();
        }

        $this->addRoleToMember($guild, end($members), $role)->then(
            function($res) use($guild, $members, $role, $deferred) { array_pop($members); return $this->addRoleToMembers($guild, $members, $role, $deferred); }, 
            function($e) use($guild, $members, $role, $deferred) { return $this->addRoleToMembers($guild, $members, $role, $deferred); }
        );
    }

    private function addRoleToMember($guild, $member, $role) 
    {
        $member->addRole($role);
        return $guild->members->save($member);
    }
}
