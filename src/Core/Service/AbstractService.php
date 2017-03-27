<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;

use \StackGuru\Core\Command\CommandContext as CommandContext;
use \StackGuru\Core\Utils\Reflection;
use \Discord\WebSockets\Event as DiscordEvent;
use \Discord\Parts\Channel\Message as Message;

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
    protected static $callbackIndex = null;
    protected static $event = null;

    public function __construct()
    {
    }

    /**
     *  Makes sure that the event member is always an array.
     *  Also adds support for using a callback with an event as parameter per event in $event[]
     */
    private function eventHandler(Callable $callback = null): array
    {
        $events = isset(static::$event[0]) ? static::$event : [static::$event];

        if (null !== $callback) {
            for ($i = 0, $end = count($events); $i < $end; $i++) {
                call_user_func_array($callback, [$events[$i]]);
            }
        }

        return $events;
    }

    // Default methods.
    // Interacts with the database to start, stop or whatever.
    // 
    public function enable(CommandContext $ctx): bool
    {
        if ("" === static::$name) {
            return false;
        }

        return $ctx->database->enableService(static::$name);
    }

    public function disable(CommandContext $ctx): bool
    {
        if ("" === static::$name) {
            return false;
        }

        return $ctx->database->disableService(static::$name);
    }
    
    // Overwritable
    public function stop(CommandContext $ctx) : bool
    {
        if (!$this->running($ctx)) {
            return false;
        }

        // remove listener from bot..
        $ctx->bot->removeStateCallable(static::$event,  static::$callbackIndex);

        $running = $this->running($ctx);

        if (!$running) {
            static::$callbackIndex = null;
        }

        return !$running;
    }

    public function start(CommandContext $ctx) : bool 
    {
        if ($this->running($ctx)) {
            return false;
        }

        // add listener to bot..
        static::$callbackIndex = $ctx->bot->state(static::$event,  [$this, "response"]);

        return $this->running($ctx);
    }

    // Generic versions.
    final public function restart(CommandContext $ctx) : bool 
    {
        $this->stop($ctx);
        $this->start($ctx);
        $this->status($ctx);
    }

    // In memory information. No database needed.
    final public function status(CommandContext $ctx) : string 
    {
        return "";
    }


    public function running(CommandContext $ctx): bool 
    {
        return null !== static::$callbackIndex && $ctx->bot->containsStateCallable(static::$event, static::$callbackIndex);
    }



    /**
     * Abstract functions
     */
    abstract public function response(string $event, string $msgId, CommandContext $serviceCtx, $data = null);



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
