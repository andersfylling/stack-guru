<?php
declare(strict_types=1);

namespace StackGuru\Core\Command;

use StackGuru\Core\Utils;
use StackGuru\Core\Command\CommandContext;


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

    protected $info;

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

        // This info can be changed in the future based on database content.
        $this->info = [
            "description" => $this->getDescription(),
            "aliases" => [],
            "activated" => true
        ];
    }


    /**
     * Setters to update info arr
     */
    public function setDescription(string $d) 
    {
        if ("" === $d) {
            return;
        }

        $this->info["description"] = $d;
    }

    public function setActivated(bool $b) 
    {
        $this->info["activated"] = $b;
    }

    public function setAliases(array $a) 
    {
        $this->info["aliases"] = $a;
    }

    public function addAlias(string $a) 
    {
        if (!isset($this->info["aliases"][$a])) {
            $this->info["aliases"][$a] = $a;
        }
    }

    public function removeAlias(string $a) 
    {
        if (!isset($this->info["aliases"][$a])) {
            return;
        }

        unset($this->info["aliases"][$a]);
    }


    public function updateInfo(array $info) 
    {
        if (isset($info["description"])) {
            $this->setDescription($info["description"]);
        }

        if (isset($info["activated"])) {
            $this->setActivated($info["activated"]);
        }

        if (isset($info["aliases"])) {
            $this->setAliases($info["aliases"]);
        }
    }

    /**
     * Aliases for getting static properties from command classes.
     */
    public function getName(): string { return $this->fqcn::getName(); }
    public function getAliases(): array { return $this->fqcn::getAliases(); }
    public function getDescription(): string { return $this->fqcn::getDescription(); }
    public function getDefault(): ?string { return $this->fqcn::getDefault(); }
    public function hasPermission(CommandContext $ctx): bool { return $this->fqcn::permitted($ctx); }


    public function getInfoAliases(): array { return $this->info["aliases"]; }
    public function getInfoDescription(): string { return $this->info["description"]; }
    public function getInfoActivated(): bool { return $this->info["activated"]; }


    /**
     * Getters for reflection properties.
     */

    public function getNamespace(): string { return $this->namespace; }
    public function getRelativeClass(): string { return $this->relativeClass; }
    public function getFullName(): string { return $this->namespace . "\\" . $this->relativeClass; }
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
