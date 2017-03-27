<?php

namespace StackGuru\Commands\Chatlog;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Last extends AbstractCommand
{
    protected static $name = "last";
    protected static $description = "Show X last messages [, within channel Y]";

    private static $printf1 = "%-5s";
    private static $printf2 = "%-15s";
    private static $printf3 = "%-18s";
    private static $printf4 = "%-20s";


    //TODO: Missing channel id parsing
    public function process(string $query, CommandContext $ctx): Promise
    {
        $limit = 10;
        $channel = null;

        $args = explode(' ', $query);

        if (is_numeric($args[0])) {
            $limit = (int) $args[0];
        }



        $messages = $ctx->database->chatlog_getLastMessages($limit, $channel);

        $response = $this->parseMessages_desktop($messages);
        return Response::sendMessage($response, $ctx->message);
    }

    public function parseMessages_desktop(array $messages): string 
    {
        $str = "";
        $str  = "```Markdown";
        $str .= PHP_EOL;
        $str .= "# ";
        $str .= sprintf(self::$printf1, "ID");
        $str .= sprintf(self::$printf2, "CHANNEL");
        $str .= sprintf(self::$printf3, "TIMESTAMP");
        $str .= sprintf(self::$printf4, "USER");
        $str .= "MESSAGE";
        $str .= PHP_EOL;

        $str = "";
        for ($i = 0, $end = sizeof($messages); $i < $end; $i++) {
            $next = $this->parseMessageDetails_desktop($messages[$i]);
            $next .= PHP_EOL;

            if (strlen($str) + strlen($next) >= 2000) {
                break;
            }
            else {
                $str .= $next;
            }
        }

        //$str .= PHP_EOL;
        //$str .= "```";

        return $str;
    }

    public function parseMessageDetails_desktop(array $message): string 
    {
        $message_id = $message["id"];
        $channel = $message["name"];
        $timestamp = $message["timestamp"];
        $user = $message["username"] . '#' . $message["discriminator"];

        // if the message content contains ` it needs to be correctly parsed
        $content = str_replace('`', "\`", $message["content"]);
        //$content = str_replace('@', "@ ", $content]); //whops

        /*

        $str .= sprintf(self::$printf1, $message["id"]);
        $str .= sprintf(self::$printf2, $message["name"]);
        $str .= sprintf(self::$printf3, $message["timestamp"]);
        $str .= sprintf(self::$printf4, $message["username"] . '#' . $message["discriminator"]);
        */


        $str = "---";
        //$str  = "```Markdown";
        $str .= PHP_EOL;
        $str .= "Channel: {$channel}, MessageID: {$message_id}";
        $str .= PHP_EOL;
        $str .= "{$user} - {$timestamp}";


        //$str .= "```";
        $str .= PHP_EOL;
        //$str .= "Message: ";
        $str .= $content;
        $str .= PHP_EOL;

        return $str;
    }
}
