<?php

namespace StackGuru\Commands\Scanner;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;
use StackGuru\Core\Utils\Response as Response;


class AddRole extends AbstractCommand
{
    protected static $name = "addrole";
    protected static $description = "gives all users a mentionable role";
    protected static $default = ""; // default sub-command

    protected $progress = 0;
    protected $totalNumberOfMembers = 0;
    protected $currentChannel = null;


    public function process(string $query, CommandContext $ctx): Promise
    {
        $deferred = new Deferred();

        if (!(strpos($query, "<@&") !== false)) {
            return Response::sendMessage("Did you mention a role?", $ctx->message);
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
        // foreach($ctx->guild->members as $member) {
        //     $member->addRole($role);
        //     $ctx->guild->members->save($member)->then(function ($response) {}, function ($e) use ($ctx) {
        //         $ctx->message->channel->sendMessage($e->getMessage());
        //     });
        // }

        $this->totalNumberOfMembers = sizeof($ctx->message->channel->guild->members->all());
        $this->currentChannel = $ctx->message->channel;

        $this->addRoleToMembersDeferred($ctx->message->channel->guild, $role)->then(function($r) use($ctx, $role, $deferred) {
            $res = "Successfully added role `{$role->name}` to every user.";
            $this->reply($res, $ctx, false, false, function() use($deferred) {
                $deferred->resolve();
            });
        });

        return $deferred;
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

        if (in_array($role, (end($members))->getRawAttributes()['roles'])) {
            array_pop($members);
            $this->updateProgress($role); 
            return $this->addRoleToMembers($guild, $members, $role, $deferred);
        }


        $this->addRoleToMember($guild, end($members), $role)->then(
            function($res) use($guild, $members, $role, $deferred) { array_pop($members); $this->updateProgress($role); return $this->addRoleToMembers($guild, $members, $role, $deferred); }, 
            function($e) use($guild, $members, $role, $deferred) { return $this->addRoleToMembers($guild, $members, $role, $deferred); }
        );
    }

    private function addRoleToMember($guild, $member, $role) 
    {
        $member->addRole($role);
        return $guild->members->save($member);
    }

    private function updateProgress($role) 
    {
        $this->progress += 1;
        $this->currentChannel->sendMessage("Adding role {$role} to {$this->progress}/{$this->totalNumberOfMembers}");
    }
}
