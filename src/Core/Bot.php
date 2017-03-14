<?php
declare(strict_types=1);

namespace StackGuru\Core;

use \Discord\Discord;
use \Discord\WebSockets\Event as DiscordEvent;
use StackGuru\Core\Utils;
use \Discord\Parts\Channel\Message as Message;
use StackGuru\Core\Command\CommandContext as CommandContext;

class Bot extends Database
{
    private $discord; // \Discord\Discord

    private $callbacks  = [
        // string "callback_name"   => [callable, callable, ... ],
    ];

    private $cmdRegistry;
    private $services;
    private $cmdAliases;


    /**
     * Bot constructor.
     *
     * @param array $options = []
     * @param bool $shutup
     */
    public function __construct(array $options = [], bool $shutup = null)
    {

        // Used for testing the bot methods
        if ($shutup !== null && $shutup === true) {
            return;
        }

        // Verify parameter to have required keys
        $options = Utils\ResolveOptions::verify($options, ["discord", "database", "commands", "services"]);

        // Setup database connection
        Database::__construct($options["database"]);

        // Setup command registry.
        $this->cmdRegistry = new \StackGuru\Core\Command\Registry();
        $this->cmdRegistry->loadCommandFolder($options["commands"]["namespace"], $options["commands"]["folder"]);

        // Store commands to database
        // 
        echo "Storing command namespaces to database";
        foreach ($this->cmdRegistry->getCommands() as $commandEntry) {
            $namespace      = $commandEntry->getFullName();
            $description    = $commandEntry->getDescription();
            $activated      = true;

            $this->saveCommand($namespace, $description, $activated);

            // Since a alias would be requested at the same level as a main command
            // We need to make an alias for each main command, that equals the normal command
            // alias == command (type of main, main is a command that can have sub commands)
            // 
            // Essentially this means u cant create an alias that equals "google" if a command named "google" exists.
            // 
            $this->saveCommandAlias($namespace, $commandEntry->getName());

            foreach ($commandEntry->getChildren() as $childEntry) {
                $namespace      = $childEntry->getFullName();
                $description    = $childEntry->getDescription();
                $activated      = true;

                $this->saveCommand($namespace, $description, $activated);
                echo ".";
            }
        }
        echo PHP_EOL;

        // Get command details from database
        //
        echo "Syncing command details with  database";
        foreach ($this->cmdRegistry->getCommands() as $commandEntry) {
            $info = $this->getCommandDetails($commandEntry->getFullName());

            $commandEntry->updateInfo($info);

            // also store each alias to an array for faster access
            foreach($info["aliases"] as $alias) {
                $this->cmdRegistry->addCommandAlias($alias, $commandEntry);
            }

            foreach ($commandEntry->getChildren() as $childEntry) {
                $info = $this->getCommandDetails($childEntry->getFullName());

                $childEntry->updateInfo($info);

                // also store each alias to an array for faster access
                foreach($info["aliases"] as $alias) {
                    $this->cmdRegistry->addCommandAlias($alias, $childEntry);
                }

                echo ".";
            }
        }
        echo PHP_EOL;


        // Debug output
        if (true === DEVELOPMENT) {
            $commands = $this->cmdRegistry->getCommands();
            echo "Loaded ", sizeof($commands), " commands:", PHP_EOL;

            $mask = "%-20s %s \n";
            printf($mask, "Name", "Subcommands");
            foreach ($commands as $name => $command) {
                $subcommands = array_keys($command->getChildren());
                printf($mask, " * " . $name, "[" . implode(", " , $subcommands) . "]");
            }
            echo PHP_EOL;
        }

        // load services
        $serviceCtx = new CommandContext();
        $serviceCtx->bot           = $this;
        $serviceCtx->guild         = null;
        $serviceCtx->cmdRegistry   = null;        
        $serviceCtx->services      = null;
        $serviceCtx->message       = null;
        $serviceCtx->discord       = null;
        $this->services = new \StackGuru\Core\Service\Services();
        $this->services->loadServicesFolder($options["services"]["namespace"], $options["services"]["folder"], $serviceCtx);


        // Debug output
        if (true === DEVELOPMENT) {
            $services = $this->services->getAll();
            echo "Loaded ", sizeof($services), " services:", PHP_EOL;

            $mask = "%-20s %s \n";
            printf($mask, "Name", "Status");
            foreach ($services as $name => $srv) {
                $status = "{\"enabled\":".($srv->isEnabled($serviceCtx)?"true":"false").", \"running\":".($srv->running()?"true":"false")."}";
                printf($mask, " * " . $name, $status);
            }
            echo PHP_EOL;
        }

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
            DiscordEvent::MESSAGE_UPDATE, // disabled due to errors in Discord
        ];

        // DiscordEvent::MESSAGE_DELETE, only returns a string $id for the message.

        // When the app is ready, listen for messages.
        $this->discord->on("ready", function (Discord $self) use ($messageEvents) {

            if (true === DEVELOPMENT) {
                echo "... OK!", PHP_EOL;
            }

            // Add bot status
            // 
            if (true === DEVELOPMENT) {
                echo "Setting bot status to `!help`.";
            }
            $game = $this->discord->factory(\Discord\Parts\User\Game::class, ["name" => "!help"]);
            if (true === DEVELOPMENT) {
                echo '.';
            }
            $this->discord->updatePresence($game, false);            
            if (true === DEVELOPMENT) {
                echo ". OK!", PHP_EOL;
            }


            // Start services
            //


            // Handle message events
            // 
            if (true === DEVELOPMENT) {
                echo "Adding bot listeners";
            }

            // New message
            $self->on(DiscordEvent::MESSAGE_CREATE, function (Message $msg, $discordObj = null) {
                // Discord has it's own exception handler, so we have to catch exceptions from
                // our message handler ourselves.
                try {
                    $this->incoming(DiscordEvent::MESSAGE_CREATE, $msg->id, $msg);

                    if (true === DEVELOPMENT) {
                        echo '.';
                    }
                } catch (\Throwable $e) {
                    echo $e, PHP_EOL;
                }
            });

            // Updated message
            $self->on(DiscordEvent::MESSAGE_UPDATE, function (Message $msg, $discordObj = null) {
                // Discord has it's own exception handler, so we have to catch exceptions from
                // our message handler ourselves.
                try {
                    $this->incoming(DiscordEvent::MESSAGE_UPDATE, $msg->id, $msg);
                    if (true === DEVELOPMENT) {
                        echo '.';
                    }
                } catch (\Throwable $e) {
                    echo $e, PHP_EOL;
                }
            });

            // Deleted message
            $self->on(DiscordEvent::MESSAGE_DELETE, function (String $msgId) {
                // Discord has it's own exception handler, so we have to catch exceptions from
                // our message handler ourselves.
                try {
                    $this->incoming(DiscordEvent::MESSAGE_DELETE, $msgId);
                    if (true === DEVELOPMENT) {
                        echo '.';
                    }
                } catch (\Throwable $e) {
                    echo $e, PHP_EOL;
                }
            });



            if (true === DEVELOPMENT) {
                echo "... OK!", PHP_EOL;
            }


            if (true === DEVELOPMENT) {
                echo "Bot is now running!", PHP_EOL, "---", PHP_EOL, PHP_EOL;
            }
        });


        // Run!
        if (true === DEVELOPMENT) {
            echo "Starting DiscordPHP service";
        }
        $this->discord->run();
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
     * If you can even call this a function.
     *
     * TODO: a method that takes any message and return a object for dealing with the content if it's a command.
     *
     * @param \Discord\Parts\Channel\Message $message
     */
    private function incoming(string $event, string $msgId, ?Message $message = null)
    {
        // if development display request
        // 
        
        if (true === DEVELOPMENT && null !== $message) {
            echo PHP_EOL, "Request: {$message->content}", PHP_EOL;
        }

        // create service context
        $serviceCtx = new CommandContext();
        $serviceCtx->bot           = $this;
        $serviceCtx->guild         = $message->channel->guild;
        $serviceCtx->cmdRegistry   = $this->cmdRegistry;       
        $serviceCtx->services      = $this->services;
        $serviceCtx->message       = $message;
        $serviceCtx->discord       = $this->discord;
        $serviceCtx->parentCommand = null;


        // First BOTEVENT::ALL_MESSAGES
        {
            $this->runScripts(BotEvent::MESSAGE_ALL_I_SELF, $event, $msgId, $message, $serviceCtx);
        }

        // This checks if the message written is by this bot itself: AKA self.
        // If its a message from self: run
        //
        // Don't continue if the message is by the bot.
        // Initiate the BOTEVENT::ALL_MESSAGES_E_SELF
        {
            if ($message->author->id == $this->discord->id) {
                $this->runScripts(BotEvent::MESSAGE_FROM_SELF, $event, $msgId, $message, $serviceCtx);
                return;
            }

            $this->runScripts(BotEvent::MESSAGE_ALL_E_SELF, $event, $msgId, $message, $serviceCtx);
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
                $usedBotReference = null !== $message->content && strpos($message->content, "<@&240626683487453184>") !== false; //@Bot
                if (!$referenced && ($mentioned || $usedBotReference)) {
                    $message->content = str_replace("<@" . $this->discord->id . ">", "", $message->content); // removes the mention of bot
                    $referenced = true; // Bot was not mentioned nor was @Bot used: <@&240626683487453184>
                }

                // Check if the bot was referenced by "!"
                if (!$referenced && null !== $message->content && substr($message->content, 0, 1) === "!") {
                    $message->content = ltrim($message->content, "!");
                    $referenced = true;
                }

                // Check if the bot wasn't referenced.
                //
                // If so, exit this function.
                if (!$referenced) {
                    $this->runScripts(BotEvent::MESSAGE_ALL_E_COMMAND, $event, $msgId, $message, $serviceCtx);
                    return;
                }

                // Since the bot has been referenced at this point, and the reference ID been stripped.
                // Remove any useless whitespaces, left of the message or command input.
                $message->content = ltrim($message->content, " ");
            }

            // show that the bot is typing
            $message->channel->broadcastTyping();

            // The incoming message is for the bot.
            $this->runScripts(BotEvent::MESSAGE_OTHERS_TO_SELF, $event, $msgId, $message, $serviceCtx);
        }



        // make sure message content is of lower case
        $message->content = strtolower($message->content);


        // Check if a help flag has been added: --help, -h. if so we just throw help in front of whatever..
        // This should be rewritten as a service...
        // 
        $flag_help = null !== $message->content && strpos($message->content, "--help") !== false;
        $flag_h = null !== $message->content && strpos($message->content, "-h") !== false;
        if (null !== $message->content && "help" !== Utils\StringParser::getFirstWord($message->content) && ($flag_h || $flag_help)) {
            $message->content = "help " . $message->content;
        }


        // It's a command. handle it.
        // Parse query to find the command and get the remaining query.
        $result = $this->cmdRegistry->parseCommandQuery($message->content);

        $command = $result["command"];
        if ($command === null) {
            Utils\Response::sendMessage("Try running `!help` :)", $message);
            return;
        }
        $query = $result["query"];

        // Create command instance
        $instance = $command->createInstance();
        $parentCmd = null == $result["parent"] ? null : $result["parent"]->createInstance();

        // Build command context so the command has references back to the bot
        // and other commands.
        $context                = new CommandContext();
        $context->bot           = $this;
        $context->guild         = $message->channel->guild;
        $context->cmdRegistry   = $this->cmdRegistry;        
        $context->services      = $this->services;
        $context->message       = $message;
        $context->discord       = $this->discord;
        $context->parentCommand = $parentCmd;
        $context->commandEntry  = $command;

        // Make sure that the user/member have permissions to use the command!
        if (!$instance->permitted($context)) {
            Utils\Response::sendMessage("You do not have authorization.", $message);
            return;
        }

        // Run command and send a response if the return is not null.
        $response = $instance->process($query, $context);
        if (null !== $response && "" !== $response) {
            Utils\Response::sendResponse($response, $message);
        }
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
     *
     * @return  int index which will always be above 0.
     */
    public function state(string $state, Callable $callback): int
    {
        if (!isset($this->callbacks[$state])) {
            $this->callbacks[$state] = [];
        }

        $index = sizeof($this->callbacks[$state]);
        $this->callbacks[$state][$index] = $callback;

        return $index;
    }

    /**
     * Removes a callback from the 2d array.
     * 
     * @param  string $state [description]
     * @param  int    $index [description]
     * @return [type]        [description]
     */
    public function removeStateCallable(string $state, int $index) 
    {
        if (null === $index || 0 > $index) {
            return;
        }

        if (isset($this->callbacks[$state]) && isset($this->callbacks[$state][$index])) {
            $this->callbacks[$state][$index] = function () {}; // empty array, doesn't scale well. Should use unset and then shift the array.
        }
    }

    /**
     * Checks if a callback request exists, and executes it.
     *
     * TODO: add parsed message content. but first create a class for it?
     *
     * @param string $state
     */
    private function runScripts(string $state, string $event, string $msgId, ?Message $message = null, CommandContext $serviceCtx)
    {
        if (!isset($this->callbacks[$state])) {
            return;
        }

        foreach ($this->callbacks[$state] as &$e) {// ($i = sizeof($arr) - 1; $i >= 0; $i -= 1) {
            call_user_func_array($e, [$event, $msgId, $message, $serviceCtx]);
            //call_user_func_array($this->callbacks[$state][$i], [$message, $event]);
        }
    }

}
