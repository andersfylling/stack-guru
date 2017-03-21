<?php
declare(strict_types=1);

namespace StackGuru\Core\Command;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred; // used in commands, pleased here so its easy to find.


/**
 * CommandInterface must be implemented by all bot commands.
 */
interface CommandInterface
{
    public function __construct();

    public static function getName(): string;
    public static function getAliases(): array;
    public static function getDescription(): string;

    // Execute the command with the given query and context.
    public function process(string $query, CommandContext $ctx): Promise;
}
