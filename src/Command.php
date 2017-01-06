<?php

namespace StackGuru;

abstract class Command implements CommandInterface
{
    protected $name = ""; // Name of the command.
    protected $aliases = []; // List of top-level aliases for the command.
    protected $description = ""; // Short summary of the commands purpose.

    // For use by CommandRegistry only.
    public $parent = null; // The parent command instance.
    public $subcommands = []; // Subcommand hashmap.

    public function __construct($options = array())
    {
        // Use class name by default as command name
        if (empty($this->name))
            $this->name = strtolower($this->getClassName());

        if (isset($options['parent']))
            $this->parent = $options['parent'];
    }

    public function getName() : string { return $this->name; }
    public function getAliases() : array { return $this->aliases; }
    public function getDescription() : string { return $this->description; }

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
        $this->subcommands[$subcommand->name] = $subcommand;
    }
}
