<?php

namespace StackGuru\Commands\Google;

class Google extends \StackGuru\Command
{
    protected $name = "google";
    protected $description = "link to google";
    protected $default = "search"; // default command

    public const URL = "https://www.google.com/";
    public const SEARCH_URL = self::URL . "search?";


    public function __construct()
    {
        parent::__construct();
    }

    public static function queryBuilder(array $query = []) : string
    {
        \StackGuru\CoreLogic\Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query);
    }

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
