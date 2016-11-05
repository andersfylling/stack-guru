<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 31.10.2016
 * Time: 21.22
 */



namespace StackGuru\CoreLogic;
use \Discord\Discord;
use \Discord\WebSockets\Event;


class Bot extends Database
{
    private $discord            = \Discord\Discord::class;

    private $callbacks = [
        // string "callback_name" => [callable, callable, ... ],
    ];

    private $commands = [
        // string "command_name" => [boolean sudo, string class, string description],
    ];

    /**
     * Bot constructor.
     *
     * @param array $options = []
     * @param bool $shutup
     */
    function __construct (array $options = [], bool $shutup = null)
    {
        if ($shutup !== null && $shutup === true) {
            return;
        }
        /*
         * Verify parameter to have required keys
         */
        $options = Utils\ResolveOptions::verify($options, ["discord", "commands", "database"]);

        /*
         * Setup database connection
         */
        Database::__construct($options["database"]);

        /*
         * Retrieve the latest commands
         */
        $this->loadCommands($options["commands"]);

        /*
         * Set up a discord instance
         */
        $this->discord = new Discord($options["discord"]);
    }

    /**
     *
     */
    public function run ()
    {
        /*
         * When the app is ready, listen for messages.
         */
        $this->discord->on("ready", function (\Discord\Discord $self) {
            $self->on(Event::MESSAGE_CREATE, function (\Discord\Parts\Channel\Message $message) {
                $this->incoming($message);
            });
        });


        /*
         * Run!
         */
        try {
            $this->discord->run();
        } catch (\Throwable $e) {
            echo $e;
        }
    }

    /**
     *
     */
    public function stop ()
    {
        /*
         * Stop discord connection
         */
        try {
            $this->discord->close();
        } catch (\Throwable $e) {
            echo $e;
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
    private function incoming (\Discord\Parts\Channel\Message $message)
    {
        /*
         * First BOTEVENT::ALL_MESSAGES
         */
        {
            $this->runScripts(\StackGuru\BotEvent::MESSAGE_ALL_I_SELF);
        }


        /*
         * This checks if the message written is by this bot itself: AKA self.
         * If its a message from self: run
         *
         * Don't continue if the message is by the bot.
         * Initiate the BOTEVENT::ALL_MESSAGES_E_SELF
         */
        {
            if ($message->author->id == $this->discord->id) {
                $this->runScripts(\StackGuru\BotEvent::MESSAGE_FROM_SELF);
                return;
            }

            $this->runScripts(\StackGuru\BotEvent::MESSAGE_ALL_E_SELF);
        }


        /*
         * Check if anyone is contacting the bot / SELF
         */
        {
            /*
             * Check if this message was written in a public.
             *  Otherwise its private => PM
             */
            if (!$message->channel->is_private) {
                /*
                 * Keeps track of whether or not the bot has been referenced.
                 */
                $referenced = false;


                /*
                 * Convert the object to an array.
                 *
                 * Needs a better to handle this. But I wasn't able to use $in->mentions->{$self->id}
                 *  to get the content I needed..
                 */
                $mentions = json_decode(json_encode($message->mentions), true);

                /*
                 * Check if the bot was referenced by either "@stack-guru" or "@Bot"
                 * Sadly I've hardcoded the mention ID for @Bot. This should be fixed somehow.
                 *
                 * TODO: this is ugly, fix it.
                 *
                 * Saves some of the if checks, otherwise these gets so long.
                 */
                $mentioned = isset($mentions[$this->discord->id]);
                $usedBotReference = strpos($message->content, "<@&240626683487453184>") !== false; //@Bot
                if (!$referenced && ($mentioned || $usedBotReference)) {
                    $message->content = str_replace("<@" . $this->discord->id . ">", "", $message->content); // removes the mention of bot
                    $referenced = true; // Bot was not mentioned nor was @Bot used: <@&240626683487453184>
                }

                /*
                 * Check if the bot was referenced by "!"
                 */
                if (!$referenced && (substr($message->content, 0, 1) === "!")) {
                    $message->content = ltrim($message->content, "!");
                    $referenced = true;
                }


                /*
                 * Check if the bot wasn't referenced.
                 *
                 * If so, exit this function.
                 */
                if (!$referenced) {
                    return;
                }

                /*
                 * Since the bot has been referenced at this point, and the reference ID been stripped.
                 * Remove any useless whitespaces, left of the message or command input.
                 */
                $message->content = ltrim($message->content, " ");
            }

            /*
             * The incoming message is for the bot.
             */
            $this->runScripts(\StackGuru\BotEvent::MESSAGE_OTHERS_TO_SELF);

        }


        /*
         * Retrieve the command and view later values, separated by whitespace, as arguments.
         *
         * eg.
         *  <@dfksj...> command arg1 arg2 arg3 arg4
         */
        $bot = [
            "command"   => "",
            "arguments" => []
        ];

        {
            $words      = explode(" ", strtolower($message->content));
            $command    = $words[0];

            /*
             * If the first word/command is a registered command, save it.
             */
            if (array_key_exists($command, $this->commands)) {
                $bot["command"] = $command;

                /*
                 * Add the arguments if there are any, and convert all to lowercase
                 */
                if (sizeof($words) > 1) {
                    $bot["arguments"] = array_slice($words, 1);
                }
            }
            else {
                Utils\Response::message("I'm sorry. It seems I cannot find your command. Please try the command: help", $message);
                return;
            }
        }


        /*
         * Initiate command
         */
        $className = $this->commands[$bot["command"]];
        $instance = new $className();

        //if the class wants it can now use the $discord instance. must be override parent class Command!
        $instance->linkDiscordObject(function () {
            return $this->discord;
        });

        $instance->command($bot["arguments"], $message);


    } // METHOD END: public incomming (\Discord\Parts\Channel\Message $in, \Discord\Discord $self)

    /**
     * Gets all the commands in given folder.
     */
    public function loadCommands (array $options = []) : array
    {
        $options = Utils\ResolveOptions::verify($options, ["folder"]);

        function dig (string $folder, bool $ignoreFiles = null) : array
        {
            $commands = [];
            foreach (glob($folder . "/*") as $path)
            {
                $file = substr(strrchr($path, "/"), 1);

                if (strpos($path, ".php") !== false) {
                    if ($ignoreFiles !== true) {
                        $commands[] = $file;
                    }
                } else {
                    $commands[] = dig($path);
                }
            }

            if ($ignoreFiles !== true) {
                return [$folder => $commands];
            } else {
                return $commands;
            }
        }

        $commands = dig($options["folder"], true);

        echo \GuzzleHttp\json_encode($commands);

        return []; //$commands;
    }



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
    public function state (string $state, Callable $callback)
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
    private function runScripts (string $state)
    {
        if (isset($this->callbacks[$state])) {
            $arr = $this->callbacks[$state];
            for ($i = sizeof($arr) - 1; $i >= 0; call_user_func($this->callbacks[$state][$i--]));
        }
    }

    public function getCommands () : string
    {
        return $this->commands;
    }

}
