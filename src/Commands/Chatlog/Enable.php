<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Enable extends AbstractCommand
{
    protected static $name = "enable";
    protected static $description = "Enable logging on a channel";
    protected static $default = ""; // default sub-command


    public function process(string $query, ?CommandContext $ctx): string
    {
        
        $output = [];
        preg_match('~<#(.*?)>~', $query, $output);

        if (2 > sizeof($output)) {
            return "Did you mention a channel using `#channelname`? Tip: you can specify multiple channels after each other.";
        }

        $counter = [];
        for ($i = 1; $i < sizeof($output); $i++) {
            $id = $output[$i];
            $counter[$id] = $ctx->bot->chatlog_setChannelAsLoggable($id);
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




        return $channelsUpdated . PHP_EOL . PHP_EOL . $channelsNotUpdated;
    }
}
