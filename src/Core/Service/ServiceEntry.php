<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;

use StackGuru\Core\Utils;


/**
 * Copied from CommandEntry.
 * TODO: cleanup
 */
class ServiceEntry
{
    protected $namespace;
    protected $class;
    protected $fullname;
    protected $instance; // a service can only run one at the time..
    // might add link to command that handles this service?...


    /**
     * Construct a Command object.
     *
     * @param string $namespace Command namespace.
     * @param string $relativeClass Class name relative to the namespace.
     */
    public function __construct(string $namespace, string $class)
    {
        $this->namespace    = rtrim($namespace, '\\');
        $this->class        = ltrim($class, '\\');

        $this->fullname     = $this->namespace . '\\' . $this->class;
        $this->instance     = null;
    }

    public function isEnabled(\StackGuru\Core\Command\CommandContext $ctx) : bool
    {
        // Check if this service exist in the database, AKA is enabled.
        // 
        return $ctx->database->doesServiceExist($this->getName());
    }


    /**
     * Aliases for getting static properties from command classes.
     */

    public function getName(): string { return $this->fullname::getName(); }
    public function getDescription(): string { return $this->fullname::getDescription(); }
    public function running(): bool { return null !== $this->instance && $this->instance->running(); }
    public function getInstance(): ?ServiceInterface { return $this->instance; }
    public function removeInstance(): bool { $this->instance = null; return null === $this->instance; }


    /**
     * Getters for reflection properties.
     */
    public function getNamespace(): string { return $this->namespace; }


    /**
     * Create a new command instance.
     *
     * @return CommandInterface Command object
     */
    public function createInstance(): ServiceInterface
    {
        $i = null;
        if (null === $this->instance) {
            $this->instance = $i = new $this->fullname;
        }
        else {
            $i = $this->instance;
        }

        return $i;
    }
}
