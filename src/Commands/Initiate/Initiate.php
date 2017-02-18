<?php

namespace StackGuru\Commands\Initiate;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Initiate extends AbstractCommand
{
    protected static $name = "initiate";
    protected static $description = "find drug information";
    protected static $default = "info"; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        $response = "";


        if (1 === sizeof($ctx->discord)) {
            // theres only one active guild, this bot is running on.
            // Store guild object in bot.
            $guild = null;
            foreach ($ctx->discord->guilds as $g) {
                $ctx->bot->guild = $guild = $g;
            }

            $response = "Set guild \"{$guild->name}\" to active guild." . PHP_EOL;
        }

        // More than one guild exists, you must specify a guild id.
        else {
            $response = "More than one guild, exists. you must specify guildid, PS. this hasn't been implemented. See Initiate.php in Commands.";
        }




        return $response;
    }
}
