<?php

namespace StackGuru;

abstract class Command implements CommandInterface
{
    protected $name = ""; // Name of the command
    protected $aliases = []; // List of aliases for the command
    protected $description = ""; // Short summary of the commands purpose

    protected $parent = null; // The parent command instance

    public function __construct($options = array())
    {
        // Use class name as default command name
        if (empty($this->name))
            $this->name = strtolower($this->get_class_name());

        if (isset($options['parent']))
            $this->parent = $options['parent'];
    }

    public static function getName() : string { return $this->name; }
    public static function getAliases() : array { return $this->aliases; }
    public static function getDescription() : string { return $this->description; }

    abstract public function process (string $query, \StackGuru\CommandContext $ctx = null) : string { }

    protected function get_class_name()
    {
        $classname = get_class($this);
        if ($pos = strrpos($classname, '\\'))
            return substr($classname, $pos + 1);
        return $classname;
    }
}
