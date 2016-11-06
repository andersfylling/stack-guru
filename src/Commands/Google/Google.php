<?php

namespace StackGuru\Commands\Google;

class Google extends \StackGuru\Commands\CommandBase
{
    const DESCRIPTION = "";
    const URL = "https://www.google.com/";

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