<?php

namespace StackGuru;

use \StackGuru\CoreLogic\Utils;
use \StackGuru\Commands;


const COMMAND_NAMESPACE = "StackGuru\\Commands";
const COMMAND_INTERFACE = "StackGuru\\Commands\\CommandInterface";


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
    const MAX_DEPTH = 10; // Maximum recursion depth for loading commands/subcommands


    // Hashmap for commands by name.
    // This only contains the top-level commands, and subcommands can be reached
    // by accessing $command->subcommands.
    private $commandsByName = [
        // "command_name" => \StackGuru\CommandInterface,
    ];

    // Hashmap for commands by alias.
    // This can contain top-level commands and subcommands, since all command aliases
    // are registered on the top-level.
    private $commandsByAlias = [
        // "command_alias" => \StackGuru\CommandInterface,
    ];

    // Hashmap for commands by fully qualified class name. For internal use only.
    private $commandReflections = null;


    /**
     * Initialize a CommandRegistry object.
     *
     * @param array $commandFolders A list of folders with command files to be loaded.
     */
    function __construct (array $commandFolders)
    {
        // Use SplObjectStorage as hashmap for command class reflections.
        $this->commandReflections = new SplObjectStorage();

        // Load all given folders into the registry.
        foreach ($commandFolders as $folder)
            $this->loadCommandFolder($folder);
    }


    /**
     * Returns the command instance and trimmed query for the latest relative command in the string.
     *
     * @param string $query The full
     *
     * @return array Instance and new query string after matched command
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

        $commands = $this->commandsByName;

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
     * @return array A hashmap of name => command.
     */
    public function getCommands () : array
    {
        return $this->commandsByName;
    }

    /**
     * Finds a registered command class by a given command path.
     * The command path may be longer than the actual
     */
    public function getCommandClass (string ...$cmdPath) : ?Command
    {
        $command = null;

        // Normalize and validate command path
        $cmdPath = Utils\Commands::normalizeCommandPath($cmdPath);

        // Find top-level command
        $topCmd = array_shift($cmdPath);
        if ($topCmd === null)
            return null;

        if (isset($this->commandsByName[$topCmd])) {
            $command = $this->commandsByName[$topCmd];
        }
        else if (isset($this->commandsByAlias[$topCmd])) {
            $command = $this->commandsByAlias[$topCmd];
        }
        if ($command == null)
            return null;

        // Traverse through subcommands
        foreach ($cmdPath as $part) {
            if (isset($command->subcommands[$part]))
                $command = $command->subcommands[$part];
            else
                return null;
        }

        return $command;
    }

    /**
     * Load all the commands in given folder(s) recursively.
     *
     * @param string $commandFolder Path to the top folder.
     *
     * @return array A list of loaded commands.
     */
    private function loadCommandFolder (string $commandFolder) : array
    {
        // Check if path exists
        if (!is_dir($commandFolder))
            throw new \RuntimeException("Folder ".$commandFolder." doesn't exist");

        // Find command files
        $commandFiles = Utils\Filesystem::dig($commandFolder, false, true);

        $commands = [];

        // Iterate over file set to load all .php files in the given folder,
        // and store an instance of the command in the registry.
        // TODO: Use SPL RecursiveDirectoryIterator to get files of all depths.
        foreach ($commandFiles as $fileSet) {
            foreach ($fileSet as $folder => $files) {
                foreach ($files as $filename) {
                    // Infer class namespace and class name from path.
                    $classNamespace = ucfirst(basename($folder));
                    $className = ucfirst(basename($filename, '.php'));

                    $command = $this->loadCommand($classNamespace, $className);
                    if ($command !== null)
                        $commands[] = $command;
                }
            }
        }

        return $commands;
    }

    private function loadCommand (string ...$path) : ?Commands\CommandReflection
    {
        // Normalize and validate command path
        $cmdPath = Utils\Commands::normalizeCommandPath($cmdPath);
        $pathLength = sizeof($cmdPath);
        if ($pathLength == 0)
            throw new RuntimeException("Invalid command path (zero length)");

        // Build fully qualified class name
        $classNameParts = array_map("ucfirst", $cmdPath);
        $fqcn = Utils\Commands::fullClassName($classNameParts)

        if (!class_exists($fqcn))
            return null;

        // Create command class reflection
        $reflect = new Commands\CommandReflection($fqcn);

        // Validate command class
        if (!$reflect->validate())
            return null;

        // if ($pathLength >= 2) {
        //     $lastCmds = array_slice($cmdPath, -2);
        //     $commandNamespace = $lastCmds[0];
        //     $commandName = $lastCmds[1];
        //
        //     // When namespace and class name are the same, the command is
        //     // the primary command for that namespace.
        //     $isPrimaryCommand = strtolower($className) === strtolower($classNamespace);
        //
        //     // Load parent/primary command for subcommand.
        //     if (!$isPrimaryCommand) {
        //         // Set parent path so that the last namespace and classname are equal.
        //         $parentPath = $cmdPath;
        //         array_pop($parentPath);
        //         $parent = $this->loadCommand(...$parentPath);
        //     }
        // }

        // Register the command class
        if (!$this->registerCommand($reflect))
            return null;

        return $reflect;
    }


    /**
     * Register a command class.
     *
     * @param ReflectionClass $reflect Reflection of a Command class.
     *
     * @return bool Success
     */
    private function registerCommand ($reflect) : bool {
        // Get fully qualified class name
        $fqcn = $reflect->getName();

        // See if command is already registered
        if ($this->commandReflections->contains($fqcn)))
            return false;

        // Validate command
        if (!Utils\Commands::validateCommandClass($reflect))
            return false;

        $commandName = $fqcn::getName();
        $commandAliases = $fqcn::getAliases();
        $commandDepth = Utils\Commands::getCommandDepth($reflect);

        echo "Registering command:", PHP_EOL;
        echo "  * Name: ", $commandName, PHP_EOL;
        echo "  * Aliases: ", implode(",", $commandAliases), PHP_EOL;
        echo "  * Depth: ", $commandDepth, PHP_EOL;

        // Register to commandReflections hashmap
        $this->commandReflections->attach($reflect);

        // Register top-level commands to commandsByName hashmap
        $depth = $reflect->getCommandDepth();
        if ($depth == 1) {
            $this->commandsByName[$commandName] = $command;
        }

        // Register aliases
        foreach ($commandAliases as $alias) {
            $this->commandsByAlias[$alias] = $command;
        }

        return true;
    }

    /**
     * Get the canonical command path (chain of command names) for a given command.
     * This is to be used for determining the key for $commands.
     *
     * TODO: This method should reside inside Command and use the commands name,
     *       rather than the class name.
     *
     * @param CommandInterface $cmd Command
     *
     * @return string Command path for command (e.g. ["google", "search"])
     */
    private function getCommandPath (CommandInterface $cmd) : string {
        // Get fully qualified class name
        $fqcn = $cmd->getFullClassName();

        // Remove top-level command namespace from class name.
        if (strpos($fqcn, self::COMMAND_NAMESPACE) === 0)
            $fqcn = substr($fqcn, strlen(self::COMMAND_NAMESPACE));

        // Trim preceding namespace separators.
        $fqcn = ltrim($fqcn, "\\");

        $path = explode("\\", $fqcn);
        $path = Utils\Commands::normalizeCommandPath($path);

        return $path;
    }
}
