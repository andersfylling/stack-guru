<?php

namespace StackGuru;

use \StackGuru\CoreLogic\Utils;


/**
 * CommandRegistry is responsible for the initialization of commands, their storage
 * and accessibility.
 *
 * It maintains a map of every command and it's subcommands, and holds information
 * about the command and an instance of the class, which can be used to execute
 * commands.
 */
class CommandRegistry
{
    private $commands   = [
        // string "command_name"    => [
        //      string "command_name"       => [boolean sudo, string class, string description],
        //      string "sub_command_name"   => [boolean sudo, string class, string description],
        // ],
    ];


    /**
     * Initialize a CommandRegistry object.
     *
     * @param array $commandFolders = []
     */
    function __construct (array $commandFolders)
    {
        var_dump($commandFolders);

        // Load all given folders into the registry.
        foreach ($commandFolders as $folder)
            $this->commands = array_merge($this->commands, $this->loadCommands($folder));
    }


    /**
     * Returns the command instance and trimmed query for the latest relative command in the string.
     *
     * @return array instance and new query string after matched command
     */
	public function parseQuery (string $query) : array
	{
        echo "Parsing query: ", $query, PHP_EOL;
        $prevCommand = null;
        $nextCommand = Utils\Commands::getFirstWordFromString($query);

        $response = [
            "query" => $query,
            "instance" => null
        ];

        $maxChain = 10;
        $depth = 0;

        $commands = $this->commands;

        while ($depth++ < $maxChain) {
            // Update command to the new first word..
            $nextCommand = Utils\Commands::getFirstWordFromString($query);

            /*
             * The first word, aka command, wasn't valid.
             * The first word is not a command.
             *
             * assumption1: $options is always an array
             *
             * assumption2: if there are no matches, it might be a default command..
             *                  Google.google = default..
             */
            if (!isset($this->commands[$nextCommand])) {
                if (null === $prevCommand || !isset($commands[$prevCommand])) {
                    return $response;
                }

                $command = $commands[$prevCommand];
                // var_dump(array(
                //     "prevCommand" => $prevCommand,
                //     "nextCommand" => $nextCommand,
                //     "command" => $command,
                // ));
                $nextCommand = strtolower($command::DEFAULT); // get default command from main command class

                // check for programming mistakes
                if (null === $nextCommand) {
                    // error!!!!!
                    throw new RuntimeException("ERROR! Commands default was null: $query");
                }
            }


            // Find command(s) in registry.
            $commands = $commands[$nextCommand]; // go into the new sub array or extract the object

            // Remove the command word from the query string if exists
            if ($nextCommand === ltrim(substr($query, 0, strlen($nextCommand))))
                $query = ltrim(substr($query, strlen($nextCommand)));
            $prevCommand = $nextCommand;

            $response["query"] = trim($query);

            // if $commands isnt an array anymore, but an object. its a match!
            if (is_object($commands)) {
                $response["instance"] = $commands;

                return $response;
            }

        }

        return $response; // assumed to never be called..
	}

    /**
     * @return array of all commands in the registry
     */
    public function getAll () : array
    {
        return $this->commands;
    }

    /**
     * @return array subcommands for given command
     */
    public function getSubcommands (string $key) : array
    {
        if (isset($this->commands[$key])) {
            return $this->commands[$key];
        }
        else {
            return null;
        }
    }

    /**
     * Get all the commands in given folder(s).
     */
    private function loadCommands (string $commandFolder) : array
    {
        // Check if path exists
        if (!is_dir($commandFolder)) {
            throw new \RuntimeException("Folder ".$commandFolder." doesn't exist");
        }

        // Find command files
        $commandFiles = Utils\Filesystem::dig($commandFolder, true);


        // Temporary store of commands only from the file set.
        $commands = array();

        // Iterate over file set to load all .php files in the given folder,
        // and store an instance of the command in the registry.
        foreach ($commandFiles as $fileSet) {
            foreach ($fileSet as $folder => $files) {
                foreach ($files as $filename) {
                    // Infer class name from filename and construct an instance of it.
                    $classNamespace = ucfirst(basename($folder));
                    $className = ucfirst(basename($filename, '.php'));
                    $class = "\\StackGuru\\Commands\\${classNamespace}\\${className}";
                    if (class_exists($class)) {
                        $interfaces = class_implements($class);
                        if (isset($interfaces["StackGuru\\CommandInterface"]) || $className === $classNamespace) {
                            $commandName = strtolower($className); // eg. Google, Service, etc.
                            $instance = new $class();
                            $commands[strtolower($classNamespace)][$commandName] = $instance;
                        }
                    }
                }
            }
        }

        return $commands;
    }
}
