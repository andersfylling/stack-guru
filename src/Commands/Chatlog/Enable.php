<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Enable extends AbstractCommand
{
    protected static $name = "enable";
    protected static $description = "Enable logging on a channel";


    public function process(string $query, CommandContext $ctx): Promise
    {
        
        $output = [];
        preg_match_all('~<#(.*?)>~', $query, $output);

        if (2 > sizeof($output) || 1 > sizeof($output[1])) {
            return "Did you mention a channel using `#channelname`? Tip: you can specify multiple channels after each other.";
        }

        $counter = [];
        for ($i = 0; $i < sizeof($output[1]); $i++) {
            $id = $output[1][$i];
            $counter[$id] = $ctx->database->chatlog_setChannelAsLoggable($id);
        }


        $channelsUpdated = "Updated loggable status for: ";
        $channelsNotUpdated = "The following channels were not updated: ";
        foreach ($counter as $id => $updated) {
            if ($updated) {
                $channelsUpdated .= "<#" . $id . "> ";
            }
            else {
                $channelsNotUpdated .= "<#" . $id . "> ";
            }
        }

        $response = $channelsUpdated . PHP_EOL . PHP_EOL . $channelsNotUpdated . PHP_EOL;
        return Response::sendMessage($response, $ctx->message);
    }
}
