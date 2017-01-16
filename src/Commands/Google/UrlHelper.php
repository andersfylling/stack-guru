<?php

namespace StackGuru\Commands\Google;

use StackGuru\Core\Utils;


const GOOGLE_URL = "https://www.google.com/";
const SEARCH_URL = GOOGLE_URL . "search?";


trait UrlHelper
{
    protected static function buildSearchUrl(array $query = []) : string
    {
        return SEARCH_URL . self::buildQuery($query);
    }

    protected static function buildQuery(array $query = []) : string
    {
        Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query);
    }
}
