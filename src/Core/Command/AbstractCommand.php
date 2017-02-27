<?php
declare(strict_types=1);

namespace StackGuru\Core\Command;

use StackGuru\Core\Utils;


abstract class AbstractCommand implements CommandInterface
{
    protected static $name = ""; // Name of the command.
    protected static $aliases = []; // List of top-level aliases for the command.
    protected static $description = ""; // Short summary of the commands purpose.

    protected static $default = null; // Default subcommand name, takes precedence over process().


    public function __construct()
    {
    }

    /**
     * Check if message author / sender has permission to use this command.
     * 
     * @return [bool] [Premitted to use this command]
     */
    final public static function permitted(CommandContext $ctx): bool 
    {
        $author = $ctx->message->author;
        $id = null;

        if (isset($author["user"])) {
            $id = $author->user->id;
        }
        else {
            $id = $author->id;
        }


        // check for alpha user
        //
        if (DISCORD_MASTER_ID == $id) {
            return true;
        }

        // if its not the alpha user, compare all the roles of the user to the roles linked to the command
        // 
        $permitted = false;
        $cmdNamespace = $ctx->commandEntry->getFullName(); // WRONG, NEED TO GET CLASS FROM THIS PLACE HERE!!
        foreach ($ctx->message->author->roles as $roleid => $role) {
            $permitted = $ctx->bot->commandHasRole($cmdNamespace, strval($roleid));
            if ($permitted) {
                break;
            }
        }

        return $permitted;
    }


    /**
     * Abstract functions
     */

    // TODO: Show Help for command by default.
    abstract public function process(string $query, CommandContext $ctx): string;


    /**
     * Getters for static command properties.
     */

    final public static function getName(): string
    {
        // Use class name by default as command name
        if (empty(static::$name))
            $name = Utils\Reflection::getShortClassName(static::class);
        else
            $name = static::$name;

        return strtolower($name);
    }

    final public static function getAliases(): array { return static::$aliases; }
    final public static function getDescription(): string { return static::$description; }
    final public static function getDefault(): ?string { return static::$default; }
}
