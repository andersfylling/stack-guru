<?php
declare(strict_types=1);

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
     * @param string $namespace Command namespace.
     * @param string $relativeClass Class name relative to the namespace.
     */
    public function __construct(string $namespace, string $relativeClass)
    {
        $this->namespace = rtrim($namespace, '\\');
        $this->relativeClass = trim($relativeClass, '\\');

        $fqcn = Utils\Reflection::getFullClass($namespace, $relativeClass);
        $this->fqcn = $fqcn;

        $this->reflection = new \ReflectionClass($fqcn);
    }


    /**
     * Aliases for getting static properties from command classes.
     */

    public function getName(): string { return $this->fqcn::getName(); }
    public function getAliases(): array { return $this->fqcn::getAliases(); }
    public function getDescription(): string { return $this->fqcn::getDescription(); }
    public function getDefault(): ?string { return $this->fqcn::getDefault(); }


    /**
     * Getters for reflection properties.
     */

    public function getNamespace(): string { return $this->namespace; }
    public function getRelativeClass(): string { $this->relativeClass; }
    public function getClass(): string { return $this->fqcn; }
    public function getCommandDepth(): int
    {
        return Utils\Commands::getCommandDepth($this->relativeClass);
    }


    /**
     * Parent/child methods.
     */

    public function getParent(): ?CommandEntry { return $this->parent; }
    public function setParent(CommandEntry $command): void
    {
        $this->parent = $command;
    }

    public function getChildren(): array { return $this->children; }
    public function getChild(string $name): ?CommandEntry
    {
        if (isset($this->children[$name]))
            return $this->children[$name];
        return null;
    }
    public function getDefaultChild(): ?CommandEntry
    {
        $name = $this->getDefault();
        if (!empty($name))
            return $this->getChild($name);
        return null;
    }
    public function addChild(CommandEntry $command): void
    {
        $name = $command->getName();

        // Check if child name is already taken - could indicate programming mistake.
        if (isset($this->children[$name]))
        {
            $child = $this->children[$name];
            throw new \ReflectionException(
                "Command name conflict for '".$name."' ".
                "between '".$command->getClass()."' and '".$child->getClass()."'");
        }

        // Set self as parent for child command
        $command->setParent($this);

        // Register child
        $this->children[$name] = $command;
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
        if (!Utils\Reflection::isInNamespace($this->namespace, $this->fqcn))
            return false;

        return true;
    }

    /**
     * Create a new command instance.
     *
     * @return CommandInterface Command object
     */
    public function createInstance(): CommandInterface
    {
        $instance = $this->reflection->newInstance();
        return $instance;
    }
}
