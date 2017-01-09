<?php

namespace StackGuru\Commands;


class CommandReflection extends \ReflectionClass
{
    private $commandNamespace = null;


    public function __construct($className, $commandNamespace) {
        parent::__construct($className);

        $this->commandNamespace = $commandNamespace;
    }

    /**
     * Validates a command class, making sure it is instantiable and implements
     * the CommandInterface.
     *
     * @param ReflectionClass $reflect Reflection of command class.
     *
     * @return bool True if class is a valid command.
     */
    public function validateCommand () : bool {
        // Don't register abstract classes, interfaces, etc.
        if (!$this->isInstantiable())
            return false;

        // Class must implement command interface
        if (!$this->implementsInterface(CommandInterface::class))
            return false;

        // Class must reside in the commands namespace
        if (!$this->inCommandNamespace())
            return false;

        return true;
    }

    public function isInNamespace (string $namespace) : bool {
        $namespace = trim($namespace, "\\") . "\\";
        return strpos($this->getName(), $namespace) === 0;
    }

    public function inCommandNamespace () : bool {
        return $this->isInNamespace($this->commandNamespace);
    }

    public function getRelativeClassName () : string {
        $fqcn = $this->getName();
        if (!$this->inCommandNamespace())
            throw new \RuntimeException("Command class ".$fqcn." does not reside in ".$this->commandNamespace);

        $relName = substr($fqcn, strlen($this->commandNamespace));
        return $relName;
    }

    /**
     * Returns the depth level of a command.
     *
     * @param ReflectionClass $reflect Reflection of command class.
     *
     * @return int Command depth
     */
    public function getCommandDepth () : int {
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
