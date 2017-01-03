<?php

namespace StackGuru\Commands\Google;

class Google extends \StackGuru\Commands\BaseCommand
{
    const DESCRIPTION = "link to google";
    const URL = "https://www.google.com/";
    const DEFAULT = "Search"; // default command

    public function __construct()
    {
        parent::__construct();
    }

    public final function queryBuilder(array $query = []) : string
    {
        \StackGuru\CoreLogic\Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query);
    }
}
