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
    const COMMAND_NAMESPACE = "StackGuru\\Commands";
    const COMMAND_INTERFACE = "StackGuru\\CommandInterface";

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
    private $commandsByClassname = [
        // "\StackGuru\Commands\Google\Google" => \StackGuru\Commands\Google\Google,
        // "\StackGuru\Commands\Google\Search" => \StackGuru\Commands\Google\Search,
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
     * @return array A hashmap of name => command.
     */
    public function getCommands () : array
    {
        return $this->commandsByName;
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

    private function getCommand (string ...$cmdPath) : ?Command
    {
        // Normalize and validate command path
        $cmdPath = Utils\Commands::normalizeCommandPath($cmdPath);
        $pathLength = sizeof($cmdPath);
        if ($pathLength == 0)
            throw new RuntimeException("Invalid command path (zero length)");

        // Find top-level command
        $firstPart = array_shift($cmdPath);
        $command = null;
        if (isset($this->commandsByName[$firstPart])) {
            $command = $this->commandsByName[$firstPart];
        }
        else if (isset($this->commandsByAlias[$firstPart])) {
            $command = $this->commandsByAlias[$firstPart];
        }
        if ($command == null)
            return null;

        // Traverse through subcommands
        foreach ($cmdPath as $part) {
            if (isset($command->subcommands[$part]))
            {
                $command = $command->subcommands[$part];
            }
            else {
                return null;
            }
        }

        return $command;
    }

    private function loadCommand (string ...$cmdPath) : ?Command
    {
        // Normalize and validate command path
        $cmdPath = Utils\Commands::normalizeCommandPath($cmdPath);
        $pathLength = sizeof($cmdPath);
        if ($pathLength == 0)
            throw new RuntimeException("Invalid command path (zero length)");

        // See if command is already registered
        $command = $this->getCommand(...$cmdPath);
        if ($command !== null)
            return $command;

        // Build fully qualified class name
        $classNameParts = array();
        foreach ($cmdPath as $cmd)
            $classNameParts[] = ucfirst($cmd);

        $fqcn = "\\" . self::COMMAND_NAMESPACE . "\\" . implode("\\", $classNameParts);
        if (!class_exists($fqcn))
            return null;

        $parent = null;

        if ($pathLength >= 2) {
            $lastCmds = array_slice($cmdPath, -2);
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
                $parent = $this->loadCommand(...$parentPath);
            }
        }

        // Register the command if it's either a primary command
        // or if it implements the command interface.
        //
        // TODO: All commands, including primary commands, should
        // implement the CommandInterface.
        $interfaces = class_implements($fqcn);
        if ($isPrimaryCommand || isset($interfaces[self::COMMAND_INTERFACE])) {
            // Initialize command
            $options = array(
                "parent" => $parent,
            );
            $command = new $fqcn($options);
            $commandName = $command->getName();
            // Add command to parent's subcommands if command has a parent
            if ($parent !== null) {
                $parent->subcommands[$commandName] = $command;
            }
            $this->registerCommand($command);
            return $command;
        }

        return null;
    }

    /**
     * Register a command instance.
     *
     * @param CommandInterface $command
     *
     * @return bool Success
     */
    private function registerCommand (CommandInterface $command) : bool {
        $fqcn = $command->getFullClassName();
        $commandName = $command->getName();
        $commandAliases = $command->getAliases();

        echo "Registering command:", PHP_EOL;
        echo "  Name: ", $commandName, PHP_EOL;
        echo "  Aliases: ", implode(",", $commandAliases), PHP_EOL;

        // See if command is already registered
        if (isset($this->commandsByClassname[$fqcn]))
            return false;

        // Register to commandsByClassname hashmap
        $this->commandsByClassname[$fqcn] = $command;

        // Register top-level commands to commandsByName hashmap
        if ($command->parent === null) {
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
