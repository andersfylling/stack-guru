<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;

use StackGuru\Core\Utils\Reflection;
use StackGuru\Core\Command\CommandContext;

/**
 * To start, stop, restart or get status about a service; Use the known linux command syntax from systemctl:
 *  !service start servicename
 *
 * When editing a service or changing settings, this should not be used. Create a command that can interact with the service.
 *
 * This class will be called from a command called Service.
 */
abstract class AbstractService implements ServiceInterface
{
    protected static $name = ""; // Name of the service.
    protected static $description = ""; // Short summary of the service purpose.
    protected static $running = false;

    public function __construct()
    {
    }

    // Default methods.
    // Interacts with the database to start, stop or whatever.
    // 
    public function enable(?CommandContext $ctx): bool
    {
        if ("" === static::$name) {
            return false;
        }

        return $ctx->bot->enableService(static::$name);
    }

    public function disable(?CommandContext $ctx) 
    {

    }
    
    // Overwritable
    public function stop(?CommandContext $ctx) : bool
    {


        self::$running = false;
    }

    public function start(?CommandContext $ctx) : bool 
    {
        self::$running = true;



    }

    // Generic versions.
    final public function restart(?CommandContext $ctx) : bool 
    {
        $this->stop($ctx);
        $this->start($ctx);
        $this->status($ctx);
    }

    // In memory information. No database needed.
    final public function status(?CommandContext $ctx) : string 
    {

    }


    public function running(): bool 
    {
        return self::$running; // not a good way to check....
    }



    /**
     * Abstract functions
     */
    //public function process(string $query, ?CommandContext $ctx): string;



    /**
     * Getters for static command properties.
     */

    final public static function getName(): string
    {
        // Use class name by default as command name
        if (empty(static::$name))
            $name = Reflection::getShortClassName(static::class);
        else
            $name = static::$name;

        return strtolower($name);
    }

    final public static function getDescription(): string 
    { 
        return static::$description; 
    }
}
