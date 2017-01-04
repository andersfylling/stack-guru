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
    const COMMAND_NAMESPACE = "\\StackGuru\\Commands";
    const COMMAND_INTERFACE = "StackGuru\\CommandInterface";

    const MAX_DEPTH = 10; // Maximum recursion depth for loading commands/subcommands


    // Nested hashmap for commands by name.
    private $commands = [
        // "command_name"    => [
        //      "command"       => \StackGuru\CommandInterface,
        //      "subcommands"   => [
        //          "command" => \StackGuru\CommandInterface,
        //          "subcommands" => []
        //      ],
        // ],
    ];

    // Hashmap for commands by fully qualified class name. For internal use only.
    private $commandsByClass = [
        // string "\StackGuru\Commands\Google\Google" => \StackGuru\Commands\Google\Google,
        // string "\StackGuru\Commands\Google\Search" => \StackGuru\Commands\Google\Search,
    ];


    /**
     * Initialize a CommandRegistry object.
     *
     * @param array $commandFolders A list of folders with command files to be loaded.
     */
    function __construct (array $commandFolders)
    {
        // Load all given folders into the registry.
        foreach ($commandFolders as $folder)
            $this->loadCommands($folder);
    }


    /**
     * Returns the command instance and trimmed query for the latest relative command in the string.
     *
     * @param string $query The full
     *
     * @return array instance and new query string after matched command
     */
	public function parseQuery (string $query) : array
	{
        $prevCommand = null;
        $nextCommand = Utils\Commands::getFirstWordFromString($query);

        $response = [
            "query" => $query,
            "instance" => null
        ];


        $depth = 0;

        $commands = $this->commands;

        while ($depth++ < $maxChain) {
            // Update command to the new first word..
            $nextCommand = Utils\Commands::getFirstWordFromString($query);

            // The first word, aka command, wasn't valid.
            // The first word is not a command.
            //
            // Assumption 1: $commands is always an array
            //
            // Assumption 2: if there are no matches, it might be a default command..
            //               e.g. Google.google = default..
            if (!isset($commands[$nextCommand])) {
                if (null === $prevCommand || !isset($commands[$prevCommand])) {
                    return $response;
                }

                $command = $commands[$prevCommand];
                $nextCommand = strtolower($command::DEFAULT); // get default command from main command class

                // Check for programming mistakes
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
     * Returns all commands that are active.
     *
     * @return array All commands in the registry.
     */
    public function getAll () : array
    {
        return $this->commands;
    }

    /**
     * Finds all the subcommands for a given command.
     *
     * @param string $key Command name
     *
     * @return array Subcommands for given command
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
     * Load all the commands in given folder(s) recursively.
     *
     * @param string $commandFolder Path to the top folder.
     *
     * @return array A map of [command]
     */
    private function loadCommands (string $commandFolder) : array
    {
        // Check if path exists
        if (!is_dir($commandFolder))
            throw new \RuntimeException("Folder ".$commandFolder." doesn't exist");

        // Find command files
        $commandFiles = Utils\Filesystem::dig($commandFolder, true);

        // Iterate over file set to load all .php files in the given folder,
        // and store an instance of the command in the registry.
        foreach ($commandFiles as $fileSet) {
            foreach ($fileSet as $folder => $files) {
                foreach ($files as $filename) {
                    // Infer class namespace and class name from path.
                    $classNamespace = ucfirst(basename($folder));
                    $className = ucfirst(basename($filename, '.php'));
                    // When filename and folder name are the same, the command
                    // is the primary command under which subcommands are hosted.
                    $isPrimaryCommand = $className === $classNamespace;

                }
            }
        }
    }

    private static function normalizeCommandPath(array $cmdPath) : array
    {
        $newCmdPath = [];
        foreach ($cmdPath as $part)
            $newCmdPath[] = strtolower($cmdPath);

        return $newCmdPath;
    }

    private function getCommand (string ...$cmdPath) : Command
    {
        // Validate command path
        $pathLength = sizeof($cmdPath);
        if ($pathLength == 0)
            throw new RuntimeException("Invalid command path (zero length)")

        // Try to find command in registry by traversing through the hashmap.
        $command = $this->commands;
        foreach ($cmdPath as $part) {
            $part = strtolower($part);
            if (!isset($command[$part])) {
                $command = null;
                break;
            }
        }

        return $command;
    }

    private function loadCommand (string ...$cmdPath) : Command
    {
        // Normalize and validate command path
        $cmdPath = self::normalizeCommandPath($cmdPath);
        $pathLength = sizeof($cmdPath)
        if ($pathLength == 0)
            throw new RuntimeException("Invalid command path (zero length)")

        // See if command is already registered
        $command = $this->getCommand($cmdPath...);
        if ($command !== null)
            return $command;

        // Build fully qualified class name
        $classNameParts = array();
        foreach ($cmdPath as $cmd)
            $classNameParts[] = ucfirst($cmd);

        $fullClassName = self::COMMAND_NAMESPACE . "\\" . implode("\\", $classNameParts);
        if (!class_exists($fullClassName))
            return null;

        $commandOptions = array(
            "parent" => null,
        );

        if ($pathLength >= 2) {
            $lastCmds = array_slice($cmds, -2);
            $classNamespace = $lastCmds[0];
            $className = $lastCmds[1];

            // When namespace and class name are the same, the command is
            // the primary command for that namespace.
            $isPrimaryCommand = strtolower($className) === strtolower($classNamespace);

            // Load parent/primary command for subcommand.
            if (!$isPrimaryCommand) {
                // Set parent path so that the last namespace and classname are equal.
                $parentPath = $cmdPath;
                $parentPath[$pathLength - 1] = $parentPath[$pathLength - 2];
                $commandOptions["parent"] = $this->loadCommand(...$parentPath);
            }
        }

        // Register the command if it's either a primary command
        // or if it implements the command interface.
        //
        // TODO: All commands, including primary commands, should
        // implement the CommandInterface.
        $interfaces = class_implements($class);
        if ($isPrimaryCommand || isset($interfaces[self::COMMAND_INTERFACE])) {
            $commandName = strtolower($className); // eg. Google, Service, etc.
            $instance = new $class($commandOptions);
            $this->registerCommand($cmdPath, $instance, true)
        }
    }

    private function registerCommand(array $cmdPath, Command $command, bool $force = null) : bool {
        // See if command is already registered
        if (!$force) {
            if ($this->getCommand(...$cmdPath) !== null)
                return false;
        }

        // Create nested hashmap inside $commands for the command path.
    }
}
