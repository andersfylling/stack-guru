<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;

class Search extends Google implements \StackGuru\CommandInterface
{
    const COMMAND_NAME = "google";
    const DESCRIPTION = "something about the search command";
    const SEARCH_URL = Google::URL . "search?";

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
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
