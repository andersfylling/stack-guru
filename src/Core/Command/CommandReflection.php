<?php

namespace StackGuru\Core\Command;


class CommandReflection extends \ReflectionClass
{
    private $namespace;


    /**
     * Construct a CommandReflection.
     *
     * @param string $namespace Namespace prefix of command.
     * @param string $fqcn Fully qualified class name of command.
     */
    public function __construct(string $namespace, string $fqcn)
    {
        parent::__construct($fqcn);

        $this->namespace = $namespace;
    }

    /**
     * Validates a command class, making sure it is instantiable and implements
     * the CommandInterface.
     *
     * @param ReflectionClass $reflect Reflection of command class.
     *
     * @return bool True if class is a valid command.
     */
    public function validateCommand () : bool
    {
        // Don't register abstract classes, interfaces, etc.
        if (!$this->isInstantiable())
            return false;

        // Class must implement command interface
        if (!$this->implementsInterface(CommandInterface::class))
            return false;

        // Class must reside in the commands namespace
        if (!$this->innamespace())
            return false;

        return true;
    }

    public function isInNamespace (string $namespace) : bool
    {
        $namespace = trim($namespace, "\\") . "\\";
        return strpos($this->getName(), $namespace) === 0;
    }

    public function inCommandNamespace () : bool
    {
        return $this->isInNamespace($this->namespace);
    }

    public function getRelativeClassName () : string
    {
        $fqcn = $this->getName();
        if (!$this->inCommandNamespace())
            throw new \RuntimeException("Command class ".$fqcn." does not reside in ".$this->namespace);

        $relName = substr($fqcn, strlen($this->namespace));
        return $relName;
    }

    /**
     * Returns the depth level of a command.
     *
     * @param ReflectionClass $reflect Reflection of command class.
     *
     * @return int Command depth
     */
    public function getCommandDepth () : int
    {
        $relName = $this->getRelativeClassName();

        $parts = explode("\\", $relName);
        $depth = sizeof($parts);

        if ($depth >= 2) {
            $classNamespace = $parts[$depth - 2];
            $className = $this->getShortName();

            // If class name is same as namespace, command is primary command,
            // therefore substract a depth level.
            if ($className == $classNamespace)
                $depth -= 1;
        }

        return $depth;
    }
}
