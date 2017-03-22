<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class View extends AbstractCommand
{
    protected static $name = "view";
    protected static $description = "View a list over channels that are being logged";



    public function process(string $query, CommandContext $ctx): Promise
    {
        $channels = $ctx->database->chatlog_getChannels();

        $helptext = "```Markdown" . PHP_EOL;
        $this->listBuggedChannels($helptext, $ctx, $channels);
        $helptext .= "```";

        return Response::sendMessage($helptext, $ctx->message);
    }

    private static function listBuggedChannels(string &$helptext, CommandContext $ctx, array $services) : string
    {
        $nr = sizeof($services);
        $helptext .= "# Channels being logged [{$nr}]" . PHP_EOL;

        // Print all services
        foreach ($services as $channel) {
            $name = $channel[0];
            $helptext .= "* {$name}" . PHP_EOL;
        }

        return $helptext;
    }
}
