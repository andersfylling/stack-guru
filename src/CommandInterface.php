<?php
/**
 * interface for all commands to be written
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru;

interface CommandInterface
{
    public function __construct();

    /*
     * Parses the give string array to do further actions.. improvements?
     */
    public function /*        */ process (string $query, CommandContext $ctx) : string;

}
