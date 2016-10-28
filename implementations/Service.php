<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 27.10.2016
 * Time: 16.23
 */

namespace Commands;

class Service extends Command
{
    function __construct()
    {
        Service::$description = "Service handler for stopping the instance.";
    }

    function command ($args, $in, $self)
    {
        /*
         * If there are not arguments behind the command, theres nothing to do here.
         */
        if (empty($args)) {
            $in->reply("You must specify an argument!");
            return;
        }

        /*
         * This Service class requires the $discord instance to work.
         *
         */
        if ($this->discord == NULL) {
            $in->reply("Discord instances was not set!");
            return;
        }

        /*
         * store first arg as action
         */
        $action = $args[0];

        /**
         * Command interactions below
         */

        /*
         * Shutdown
         *
         * ISSUE: won't reply back that service is shut down.. stupid design mistake by DiscordPHP
         */
        if ($action == "shutdown") {
            $in->reply("Shutting down..");
            $in->reply("Bye bye!")->then(function () {
                $this->discord->close();

                echo "Shutting down!", PHP_EOL;
                exit();
            });
        }

        /*
         * Restart
         */
        if ($action == "restart") {
            // Restart logic...
        }
    }

    /**
     * Returns $discord instance
     *
     * @param $callback
     * @return mixed
     */
    function discordRelated ($callback)
    {
        $this->discord = $callback();
    }
}