<?php

namespace StackGuru\Core\Command;


/**
 * CommandInterface must be implemented by all bot commands.
 */
interface CommandInterface
{
    public function __construct();

    public static function getName(): string;
    public static function getAliases(): array;
    public static function getDescription(): string;
    public static function getDefault(): ?string;

    // Execute the command with the given query and context.
    public function process(string $query, ?CommandContext $ctx): string;
}
