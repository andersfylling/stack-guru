<?php

namespace StackGuru\Commands\Google;

class Google extends \StackGuru\Commands\BaseCommand
{
    protected static $name = "google";
    protected static $description = "link to google";
    protected static $default = "search"; // default command

    public const URL = "https://www.google.com/";
    public const SEARCH_URL = self::URL . "search?";


    public static function queryBuilder(array $query = []) : string
    {
        \StackGuru\CoreLogic\Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query);
    }

    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
