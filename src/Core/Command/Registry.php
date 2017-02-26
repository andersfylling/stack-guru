<?php
declare(strict_types=1);

namespace StackGuru\Core\Command;

use \StackGuru\Core\Utils;


/**
 * Registry is responsible for the initialization of commands, their storage
 * and accessibility.
 *
 * It maintains a map of every command and it's subcommands, and holds information
 * about the command and an instance of the class, which can be used to execute
 * commands.
 */
class Registry
{
    const MAX_DEPTH = 10; // Maximum recursion depth for loading commands/subcommands

    use QueryRouter;


    // Associative array for commands by name.
    // This only contains the top-level commands, and subcommands can be reached
    // by accessing $command->subcommands.
    private $commands = [
        // "command_name" => \StackGuru\Commands\CommandEntry,
    ];

    // Hashmap for commands by alias.
    // This can contain top-level commands and subcommands, since all command aliases
    // are registered on the top-level.
    private $commandAliases = [
        // "command_alias" => \StackGuru\Commands\CommandEntry,
    ];

    // Associative array for command nodes by fully qualified class name.
    // For internal use only.
    private $commandClasses = [
        // "\StackGuru\Commands\Google\Search" => \StackGuru\Commands\CommandEntry
    ];


    public function __construct()
    {
    }

    /**
     * Returns command tree.
     *
     * @return array A hashmap of name => command.
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Returns all command classes.
     *
     * @return array A hashmap of name => command.
     */
    public function getCommandClasses(): array
    {
        return $this->commandClasses;
    }

    public function getCommandAliases(): array 
    {
        return $this->commandAliases;
    }

    public function addCommandAlias(string $alias, CommandEntry $entry) 
    {
        if (null === $entry || isset($this->commandAliases[$alias])) {
            return;
        }

        return $this->commandAliases[$alias] = $entry;
    }

    public function getCommandFromAlias(string $alias): ?CommandEntry 
    {
        if (isset($this->commandAliases[$alias])) {
            return $this->commandAliases[$alias];
        }

        return null;
    }

    /**
     * Load all the commands in given folder(s) recursively.
     *
     * @param string $namespace Full namespace of the folder (must begin with \).
     * @param string $path Path to the folder containing commands.
     *
     * @return array A list of loaded commands.
     */
    public function loadCommandFolder(string $namespace, string $path): array
    {
        // Fix namespace if not fully qualified
        if (substr($namespace, 0, 1) != '\\')
            $namespace = '\\' . $namespace;
        $namespace = rtrim($namespace, '\\');

        // Check if path exists
        if (!is_dir($path))
            throw new \RuntimeException("Folder ".$path." doesn't exist");

        // Find command files
        $commandFiles = Utils\Filesystem::dig($path, false, true);

        $commands = [];

        // Iterate over file set to load all .php files in the given folder,
        // and store an instance of the command in the registry.
        //
        // TODO: Use SPL RecursiveDirectoryIterator to get files of all depths.
        foreach ($commandFiles as $fileSet) {
            foreach ($fileSet as $folder => $files) {
                foreach ($files as $filename) {
                    // Infer relative command namespace and class name from path.
                    $relativeNamespace = ucfirst(basename($folder));
                    $className = ucfirst(basename($filename, '.php'));

                    $relativeClass = $relativeNamespace . '\\' . $className;

                    $command = $this->loadCommand($namespace, $relativeClass);
                    if ($command !== null)
                        $commands[] = $command;
                }
            }
        }

        return $commands;
    }

    /**
     * Load a command class into the registry and return node object.
     * If the command is already loaded, the existing node is returned.
     *
     * @param string $namespace Full command namespace prefix (must begin with \).
     * @param string $relativeClass Class name relative to the namespace.
     *
     * @return ?CommandEntry Command node.
     */
    public function loadCommand(string $namespace, string $relativeClass): ?CommandEntry
    {
        // Check if namespace is fully qualified
        if (substr($namespace, 0, 1) != '\\')
            throw new \RuntimeException("Namespace ".$namespace." is not fully qualified");

        $fqcn = Utils\Reflection::getFullClass($namespace, $relativeClass);

        // Check if class exists and try to autoload it
        if (!class_exists($fqcn, true))
            return null;

        $command = $this->createCommandEntry($namespace, $relativeClass);
        if ($command === null)
            return null;

        // See if command is already registered
        if (isset($this->commandClasses[$fqcn]))
            return $this->commandClasses[$fqcn];

        // Get command properties
        $commandName = $command->getName();
        $commandAliases = $command->getAliases();

        // Register command with parent, if exists
        $parentClass = Utils\Commands::getParentClass($relativeClass);
        if ($parentClass !== null) {
            $parent = $this->loadCommand($namespace, $parentClass);
            if ($parent !== null) {
                $parent->addChild($command);
            }
        }

        // Register top-level commands to command tree
        if (Utils\Commands::isTopLevelCommand($relativeClass)) {
            if (isset($this->commands[$commandName]))
                throw new \ReflectionError("Command '".$name."' already exists");

            $this->commands[$commandName] = $command;
        }

        // Register aliases
        foreach ($commandAliases as $alias) {
            if (isset($this->commandAliases[$alias]))
                throw new \ReflectionError("Command alias '".$alias."' already exists");

            $this->commandAliases[$alias] = $command;
        }

        // Register class
        $this->commandClasses[$fqcn] = $command;

        return $command;
    }

    private function createCommandEntry(string $namespace, string $relativeClass): ?CommandEntry
    {
        try {
            // Build command node
            $command = new CommandEntry($namespace, $relativeClass);

            // Validate command class
            if (!$command->validate())
                return null;

            return $command;
        } catch (\ReflectionException $e) {
            echo "Error creating command entry: ", $e->getMessage(), PHP_EOL;

            return null;
        }

        return null;
    }

    /**
     * Finds a registered command class by a given command path.
     * The command path may be longer than the actual path to the command,
     * so that a full query can be used as argument.
     */
    public function getCommandClass(string ...$cmdPath): ?CommandEntry
    {
        $command = null;

        // Normalize and validate command path
        $cmdPath = Utils\Commands::normalizeCommandPath($cmdPath);

        // Find top-level command
        $topCmd = array_shift($cmdPath);
        if ($topCmd === null)
            return null;

        // Search names and aliases
        if (isset($this->commands[$topCmd])) {
            $command = $this->commands[$topCmd];
        } else if (isset($this->commandAliases[$topCmd])) {
            $command = $this->commandAliases[$topCmd];
        }

        if ($command == null)
            return null;

        // Traverse through subcommands
        foreach ($cmdPath as $part) {
            $child = $command->getChild($part);
            if ($child !== null)
                $command = $child;
            else
                break;
        }

        return $command;
    }
}
