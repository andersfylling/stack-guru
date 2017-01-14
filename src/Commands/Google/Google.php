<?php

namespace StackGuru\Commands\Google;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;


class Google extends AbstractCommand
{
    public const URL = "https://www.google.com/";
    public const SEARCH_URL = self::URL . "search?";

    protected static $name = "google";
    protected static $description = "link to google";
    protected static $default = "search"; // default command


    public function process (string $query, ?CommandContext $ctx) : string
    {
        return "Not implemented yet";
    }
}
