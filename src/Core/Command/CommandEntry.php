<?php

namespace StackGuru\Core\Command;

use StackGuru\Core\Utils;


/**
 * CommandEntry is a node inside a tree structure in the command registry.
 * It holds information about the command class, it's parent and children commands,
 * and the ability to create a new instance of it.
 */
class CommandEntry
{
    protected $namespace;
    protected $relativeClass;
    protected $fqcn;

    protected $reflection; // \ReflectionClass
    protected $parent; // ?CommandEntry
    protected $children = []; // [ "name" => CommandEntry, ... ]

    /**
     * Construct a Command object.
     *
     * @param string $namespace Full command namespace (must begin with \).
     * @param string $relativeClass Class name relative to the namespace.
     */
    public function __construct(string $namespace, string $relativeClass)
    {
        $this->namespace = $namespace;
        $this->relativeClass = $relativeClass;

        $fqcn = Utils\Reflection::getFullClass($namespace, $relativeClass);
        $this->fqcn = $fqcn;

        $this->reflection = new \ReflectionClass($fqcn);
    }

    public function getParent(): ?Command
    {
        return $this->parent;
    }

    public function setParent(CommandEntry $parent): void
    {
        $this->parent = $parent;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChild(string $name): ?Command
    {
        if (isset($this->children[$name]))
            return $this->children[$name];
        return null;
    }

    public function addChild(CommandEntry $child): void
    {
        $name = $child->getName();

        // Check if child name is already taken - could indicate programming mistake.
        if (isset($this->children[$name]))
        {
            throw new \ReflectionException(
                "Command name conflict for '".$name."' ".
                "between '".$child->getClass()."' and '".$this->children[$name]->getClass()."'");
        }

        // Set self as parent for child command
        $child->setParent($this);

        // Register child
        $this->children[$name] = $child;
    }

    public function getClass(): string
    {
        return $this->fqcn;
    }

    public function getRelativeClass(): string
    {
        return Utils\Reflection::getRelativeClass($this->namespace, $this->fqcn);
    }

    public function getName(): string
    {
        return $this->fqcn::getName();
    }

    public function getAliases(): array
    {
        return $this->fqcn::getAliases();
    }

    public function getDescription(): string
    {
        return $this->fqcn::getDescription();
    }

    /**
     * Returns the depth level of a command.
     *
     * @return int Command depth
     */
    public function getDepth(): int
    {
        $relativeClass = $this->getRelativeClass();

        $parts = explode("\\", $relativeClass);
        $depth = sizeof($parts);
        $primary = Utils\Commands::isPrimaryCommand($relativeClass);

        // If class name is same as namespace, command is primary command,
        // therefore substract a depth level.
        if ($primary)
            $depth -= 1;

        return $depth;
    }

    /**
     * Validates a command class, making sure it is instantiable and implements
     * the CommandInterface.
     *
     * @return bool True if class is a valid command.
     */
    public function validate(): bool
    {
        // Don't register abstract classes, interfaces, etc.
        if (!$this->reflection->isInstantiable())
            return false;

        // Class must implement command interface
        if (!$this->reflection->implementsInterface(CommandInterface::class))
            return false;

        // Class must reside in the commands namespace
        if (!Utils\Reflection::isInNamespace($this->fqcn, $this->namespace))
            return false;

        return true;
    }

    public function createInstance(): CommandInterface
    {
        $instance = $this->reflection->newInstance();
        return $instance;
    }
}
