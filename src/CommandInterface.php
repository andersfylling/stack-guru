<?php
/**
 * interface for all commands to be written
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru;

/**
 *
 */
interface CommandInterface
{
    public function __construct();

    public function getName() : string;
    public function getAliases() : array;
    public function getDescription() : string;

    // Execute the command with the given query and context.
    public function process (string $query, CommandContext $ctx) : string;

}
