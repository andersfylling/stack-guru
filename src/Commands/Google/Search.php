<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;

class Search extends \StackGuru\Commands\BaseCommand
{
    protected static $name = "search";
    protected static $description = "search";


    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        $args = explode(' ', $query);

        if (sizeof($args) >= 1 && 0 !== strlen($args[0])) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Search::SEARCH_URL . $this->queryBuilder(['q' => $query]);

        return $link;
    }
}
