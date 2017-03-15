<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;


abstract class Response
{
    /**
     * @param string $str
     * @param \Discord\Parts\Channel\Message|null $message
     * @param \Closure|null $callback
     * @param bool|null $private
     */
    public static function sendResponse(
        string                          $str,
        \Discord\Parts\Channel\Message  $message    = null,
        boolean                         $private    = null,
        ?\Closure                       $callback   = null
    ) {
        if (null === $message && true === DEVELOPMENT) {
            echo "Message was not sent: {$str}" . PHP_EOL;
            return;
        }

        /*
         * Check if the channel is private or not
         */
        if (null !== $private) {
            /*
             * For some reason, the author object differs when its a private chat compared to public.
             */

            if (true === DEVELOPMENT) {
                echo "Response: {$str}", PHP_EOL;
            }

            if (false === TESTING) {
                $message->getAuthorAttribute(0)->sendMessage($str)->then($callback);
            }
        }

        /*
         * If this is to be sent in public, utilize the already existing reply method.
         * $in->author->((user->)*)sendMessage("{$in->author}, {$message}");
         */
        else {
            if (true === DEVELOPMENT) {
                echo "Response: {$str}", PHP_EOL;
            }

            if (false === TESTING) {
                $message->reply($str)->then($callback);
            }
        }
    }


    /**
     * Send a message to the same channel the message came from. maybe rename this to sendUpdate(...)
     * 
     * @param string $str
     * @param \Discord\Parts\Channel\Message|null $message
     * @param \Closure|null $callback
     * @param bool|null $private
     */
    public static function sendMessage(
        string                          $str,
        \Discord\Parts\Channel\Message  $message,
        ?\Closure                       $callback = null
    ) {
        if (null === $message && true === DEVELOPMENT) {
            echo "Message was not sent: {$str}" . PHP_EOL;
            return;
        }

        if (true === DEVELOPMENT) {
            echo "Response: {$str}", PHP_EOL;
        }

        if (false === TESTING) {
            $message->channel->sendMessage($str)->then($callback);
        }
    }
}
