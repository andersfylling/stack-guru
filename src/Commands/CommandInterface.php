<?php
/**
 * interface for all commands to be written
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru\Commands;

/**
 *
 */
interface CommandInterface
{
    public function __construct();

    public static function getName() : string;
    public static function getAliases() : array;
    public static function getDescription() : string;

    public function getParent() : ?CommandInterface;

    // Execute the command with the given query and context.
    public function process (string $query, CommandContext $ctx) : string;

}
