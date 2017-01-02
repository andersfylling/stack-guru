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


        // un expected bahavior: explode on empty string does not return an empty array.. ffs.
        // this still gives fucking errors. fuck you php!
        // its supposed to have a size of 0, still its 1, and then i get the undefined offset on $args[1]
        // WTF.
        if (sizeof($args) >= 1 && 0 !== strlen($args[1])) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Search::SEARCH_URL . $this->queryBuilder(['q' => $query]);

        return $link;
    }
}
