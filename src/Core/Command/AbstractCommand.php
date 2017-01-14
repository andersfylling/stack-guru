<?php

namespace StackGuru\Core\Command;

abstract class AbstractCommand implements CommandInterface
{
    protected static $name = ""; // Name of the command.
    protected static $aliases = []; // List of top-level aliases for the command.
    protected static $description = ""; // Short summary of the commands purpose.

    public function __construct()
    {
    }

    // TODO: Show Help for command by default.
    abstract public function process (string $query, ?CommandContext $ctx) : string;

    final public static function getName() : string
    {
        // Use class name by default as command name
        $name = empty(static::$name) ? static::getClassName() : static::$name;

        return strtolower($name);
    }

    final public static function getAliases() : array
    {
        return static::$aliases;
    }
    final public static function getDescription() : string
    {
        return static::$description;
    }

    final public static function getClassName() : string
    {
        return substr(strrchr(static::class, '\\'), 1);
    }
}
