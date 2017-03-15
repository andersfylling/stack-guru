<?php

namespace StackGuru\Commands\Google;

use StackGuru\Core\Utils;


const GOOGLE_URL = "https://www.google.com/";
const SEARCH_URL = GOOGLE_URL . "search?";


trait UrlHelper
{
	//protected
    public static function buildSearchUrl(array $query = []) : string
    {
        return SEARCH_URL . self::buildQuery($query);
    }

    //protected
    public static function buildQuery(array $query = []) : string
    {
        Utils\ResolveOptions::verify($query, []); // anything is allowed

        return http_build_query($query, '', '&'); // +& is seen between two elements of the query array...
    }
}
