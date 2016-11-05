<?php

namespace StackGuru\Commands\Google;

class Search extends Google implements \StackGuru\Commands\CommandInterface
{
    const DESCRIPTION = "something about the search command";
    const SEARCH_URL = Google::URL . "search?";

    public function response (array $args = []) : string
    {
        if (sizeof($args) !== 0) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Search::SEARCH_URL . $this->queryBuilder(['q' => $query]);

        return $link;
    }
}