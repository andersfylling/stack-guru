<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;


trait UrlHelper
{
    protected static function buildSearchUrl(array $query = []) : string
    {
        return Google::SEARCH_URL . $this->buildQuery($query);
    }

    protected static function buildQuery(array $query = []) : string
    {
        Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query);
    }
}
