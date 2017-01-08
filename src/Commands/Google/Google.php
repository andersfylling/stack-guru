<?php

namespace StackGuru\Commands\Google;

class Google extends \StackGuru\Commands\BaseCommand
{
    public const URL = "https://www.google.com/";
    public const SEARCH_URL = self::URL . "search?";

    protected static $name = "google";
    protected static $description = "link to google";
    protected static $default = "search"; // default command

    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
