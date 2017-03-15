<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;
use StackGuru\Core\Command\CommandContext;


/**
 * CommandInterface must be implemented by all bot commands.
 */
interface ServiceInterface
{
    public function __construct();

    public static function getName(): string;
    public static function getDescription(): string;

    // default methods.
    public function stop(CommandContext $ctx) 		: bool;
    public function start(CommandContext $ctx) 	: bool;
    public function restart(CommandContext $ctx) 	: bool;
    public function status(CommandContext $ctx) 	: string;

    public function running(): bool;

    // Execute the command with the given query and context.
    public function process(string $query, ?CommandContext $ctx): string;
}
