<?php

namespace StackGuru\Commands\Say;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;
use StackGuru\Core\Utils\Response as Response;


class Say extends AbstractCommand
{
    protected static $name = "say";
    protected static $description = "Make the bot say something";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $flag_channel = null;

        if (strpos($query, "--channel") !== false) {
            $flag_channel = "--channel";
        } 
        else if (strpos($query, "-c") !== false) {
            $flag_channel = "-c";
        }

        if (null !== $flag_channel) {
            $output = null;
            $channelContent = end(explode($flag_channel, $query));
            preg_match("~<#(.*?)>~", $channelContent, $output);

            if (2 <= sizeof($output) && isset($ctx->message->channel->guild->channels[$output[1]])) {
                //$channel = $ctx->message->channel->guild->channels[$output[1]];
                //$ctx->message->channel = $channel;
                $ctx->message->channel_id = $output[1];
            }
        }

        $message = null;
        $flag_message = null;
        if (strpos($query, "--message") !== false) {
            $flag_message = "--message";
        } 
        else if (strpos($query, "-m") !== false) {
            $flag_message = "-m";
        }

        if (null !== $flag_message) {
            $message = end(explode($flag_message . ' ', $query));
        } 
        else {
            $message = "Hi!";
        }

        var_dump($ctx->message);


        return Response::sendMessage($message, $ctx->message);
    }
}
