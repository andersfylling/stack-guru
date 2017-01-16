<?php

namespace StackGuru\Core;

use \Discord\Discord;
use \Discord\WebSockets\Event as DiscordEvent;


class Bot extends Database
{
    private $discord; // \Discord\Discord

    private $callbacks  = [
        // string "callback_name"   => [callable, callable, ... ],
    ];


    /**
     * Bot constructor.
     *
     * @param array $options = []
     * @param bool $shutup
     */
    function __construct(array $options = [], bool $shutup = null)
    {
        // Used for testing the bot methods
        if ($shutup !== null && $shutup === true) {
            return;
        }

        // Verify parameter to have required keys
        $options = Utils\ResolveOptions::verify($options, ["discord", "database"]);

        // Setup database connection
        Database::__construct($options["database"]);

        // Set up a discord instance
        $this->discord = new Discord($options["discord"]);
    }

    /**
     * Setup event handlers and run the bot.
     */
    public function run()
    {
        // Events to trigger a message update.
        $messageEvents = [
            DiscordEvent::MESSAGE_CREATE,
            // DiscordEvent::MESSAGE_UPDATE, // disabled due to errors in DiscordPHP
            DiscordEvent::MESSAGE_DELETE,
            DiscordEvent::MESSAGE_DELETE_BULK
        ];

        // When the app is ready, listen for messages.
        $this->discord->on("ready", function (Discord $self) use ($messageEvents) {
            // Message events
            foreach ($messageEvents as $event) {
                $self->on($event, function (\Discord\Parts\Channel\Message $message) use ($event) {
                    // Discord has it's own exception handler, so we have to catch exceptions from
                    // our message handler ourselves.
                    try {
                        $this->incoming($message, $event);
                    } catch (\Throwable $e) {
                        echo $e, PHP_EOL;
                    }
                });
            }
        });


        // Run!
        try {
            $this->discord->run();
        } catch (\Throwable $e) {
            echo $e, PHP_EOL;
        }
    }

    /**
     * Stop the bot.
     */
    public function stop()
    {
        /*
         * Stop discord connection
         */
        try {
            $this->discord->close();
        } catch (\Throwable $e) {
            echo $e, PHP_EOL;
        }
    }

    /**
     * Probably the worst function i've written in my life.
     *  If you can even call this a function.
     *
     * TODO: a method that takes any message and return a object for dealing with the content if it's a command.
     *
     * @param \Discord\Parts\Channel\Message $message
     */
    private function incoming(\Discord\Parts\Channel\Message $message, string $event)
    {
        // First BOTEVENT::ALL_MESSAGES
        {
            $this->runScripts(BotEvent::MESSAGE_ALL_I_SELF, $message, $event);
        }

        // This checks if the message written is by this bot itself: AKA self.
        // If its a message from self: run
        //
        // Don't continue if the message is by the bot.
        // Initiate the BOTEVENT::ALL_MESSAGES_E_SELF
        {
            if ($message->author->id == $this->discord->id) {
                $this->runScripts(BotEvent::MESSAGE_FROM_SELF, $message, $event);
                return;
            }

            $this->runScripts(BotEvent::MESSAGE_ALL_E_SELF, $message, $event);
        }

        // Check if anyone is contacting the bot / SELF
        {
            // Check if this message was written in a public.
            // Otherwise its private => PM
            if (!$message->channel->is_private) {
                // Keeps track of whether or not the bot has been referenced.
                $referenced = false;

                // Convert the object to an array.
                //
                // Needs a better to handle this. But I wasn't able to use $in->mentions->{$self->id}
                // to get the content I needed..
                $mentions = json_decode(json_encode($message->mentions), true);

                // Check if the bot was referenced by either "@stack-guru" or "@Bot"
                // Sadly I've hardcoded the mention ID for @Bot. This should be fixed somehow.
                //
                // TODO: this is ugly, fix it.
                //
                // Saves some of the if checks, otherwise these gets so long.
                $mentioned = isset($mentions[$this->discord->id]);
                $usedBotReference = strpos($message->content, "<@&240626683487453184>") !== false; //@Bot
                if (!$referenced && ($mentioned || $usedBotReference)) {
                    $message->content = str_replace("<@" . $this->discord->id . ">", "", $message->content); // removes the mention of bot
                    $referenced = true; // Bot was not mentioned nor was @Bot used: <@&240626683487453184>
                }

                // Check if the bot was referenced by "!"
                if (!$referenced && (substr($message->content, 0, 1) === "!")) {
                    $message->content = ltrim($message->content, "!");
                    $referenced = true;
                }

                // Check if the bot wasn't referenced.
                //
                // If so, exit this function.
                if (!$referenced) {
                    return;
                }

                // Since the bot has been referenced at this point, and the reference ID been stripped.
                // Remove any useless whitespaces, left of the message or command input.
                $message->content = ltrim($message->content, " ");
            }

            // The incoming message is for the bot.
            $this->runScripts(BotEvent::MESSAGE_OTHERS_TO_SELF, $message, $event);
        }
    } // METHOD END: public incoming (\Discord\Parts\Channel\Message $in, \Discord\Discord $self)



    /* ********************************************
     * callbacks for dealing with scripts........
     * *******************************************/

    /**
     * Stores requested callbacks.
     * Since there can be more than one callback with the same type,
     *  store each in a auto incremented index as child of the request type.
     *
     * @param string $state
     * @param \Closure $callback
     */
    public function state(string $state, Callable $callback)
    {
        $this->callbacks[$state][] = $callback;
    }

    /**
     * Checks if a callback request exists, and executes it.
     *
     * TODO: add parsed message content. but first create a class for it?
     *
     * @param string $state
     */
    private function runScripts(string $state, \Discord\Parts\Channel\Message $message, string $event)
    {
        if (!isset($this->callbacks[$state])) {
            return;
        }
        $arr = $this->callbacks[$state];
        for ($i = sizeof($arr) - 1; $i >= 0; $i -= 1) {
            call_user_func_array($this->callbacks[$state][$i], [$message, $event]);
        }
    }

}
