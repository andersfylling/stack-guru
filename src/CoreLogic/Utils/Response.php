<?php
/**
 * Created by PhpStorm.
 * User: anders
 * Date: 11/5/16
 * Time: 3:12 PM
 */

namespace StackGuru\CoreLogic\Utils;


class Response
{


    /**
     * @param string $str
     * @param \Discord\Parts\Channel\Message|null $message
     * @param \Closure|null $callback
     * @param bool|null $private
     */
    public static function sendResponse (
        string                          $str,
        \Discord\Parts\Channel\Message  $message    = null,
        boolean                         $private    = null,
        \Closure                        $callback   = null
    ) {
        if (null === $message) {
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

            /*
             * Private
             */
            if (true === $message->channel->is_private) {
                if (DEVELOPMENT) {
                    echo "Response: {$str}", PHP_EOL;
                }
                
                if (!TESTING) {
                    $message->author->sendMessage($str)->then($callback);
                }
            }

            /*
             * Public
             */
            else {
                if (true === DEVELOPMENT) {
                    echo "Response: {$str}", PHP_EOL;
                }
                
                if (false === TESTING) {
                    $message->author->user->sendMessage($str)->then($callback);
                }
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
}
