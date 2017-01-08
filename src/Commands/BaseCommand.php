<?php

namespace StackGuru\Commands;

abstract class BaseCommand implements CommandInterface
{
    protected static $name = ""; // Name of the command.
    protected static $aliases = []; // List of top-level aliases for the command.
    protected static $description = ""; // Short summary of the commands purpose.

    // For use by CommandRegistry only.
    protected $parent = null; // The parent command instance.
    protected $subcommands = []; // Subcommand hashmap.

    public function __construct($options = array())
    {
        // Use class name by default as command name
        if (empty(static::name))
            static::name = strtolower($this->getClassName());

        if (isset($options['parent']))
            $this->parent = $options['parent'];
    }

    public static function getName() : string { return static::$name; }
    public static function getAliases() : array { return static::$aliases; }
    public static function getDescription() : string { return static::$description; }

    public function getParent() : ?CommandInterface { return $this->parent; }
    public function getSubcommands() : array { return $this->subcommands; }

    abstract public function process (string $query, CommandContext $ctx = null) : string;

    final public function getFullClassName() : string
    {
        return get_class($this);
    }

    final public function getClassName() : string
    {
        $classname = $this->getFullClassName();
        if ($pos = strrpos($classname, '\\'))
            return substr($classname, $pos + 1);
        return $classname;
    }

    final public function addSubcommand(CommandInterface $subcommand) {
        $name = $subcommand->getName();
        $this->subcommands[$name] = $subcommand;
    }
}
