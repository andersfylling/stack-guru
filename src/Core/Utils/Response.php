<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;
use \React\Promise\Deferred as Deferred;
use \Discord\Parts\Channel\Message as Message;
use \StackGuru\Core\Utils\Logger as Logger;
use \StackGuru\Core\Utils\DebugLevel as Level;


abstract class Response
{
    /**
     * @param string $str The message content
     * @param \Discord\Parts\Channel\Message|null $message
     * @param bool|null $mention Should the user be mentioned or not
     * @param bool|null $private if this is to be sent as a pm
     */
    public static function sendMessage(string $str, Message $message = null, bool $mention = false, bool $private = false): Deferred
    {
        $deferred = new Deferred();
        $testing = defined("TESTING") && TESTING;


        if (null === $message) {
            Logger::log(Level::INFO, "Message was not sent: {$str}");

            $deferred->reject('$message was null'); // should this be an \Exception ?
            return $deferred;
        }


        // if this is used in an testing environment,
        if (!$testing) {
            // not testing so lets send the message,
            if ($private) {
                // send the message to the user only,
                $deferred = $message->getAuthorAttribute(0)->sendMessage($str);
            }
            else {
                // send it public, to a server/guild channel,
                if ($mention) {
                    // mention the user first,
                    $deferred = $message->reply($str);
                }
                else {
                    // don't mention the user.
                    $deferred = $message->channel->sendMessage($str);
                }
            }

            // Log that the message was "sent" from the bot
            Logger::log(Level::INFO, "Added message to DiscordPHP que: {$str}");
        }
        else {
            $deferred->resolve(); // in testing, didn't send message (to avoid spam)
        }


        return $deferred;
    }
}