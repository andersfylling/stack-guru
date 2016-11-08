<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;

class Search extends Google implements \StackGuru\Commands\CommandInterface
{
    const COMMAND_NAME = "google";
    const DESCRIPTION = "something about the search command";
    const SEARCH_URL = Google::URL . "search?";

    public function process (array $args = [], \Discord\Parts\Channel\Message $in = null) : string
    {
        if (sizeof($args) !== 0) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Search::SEARCH_URL . $this->queryBuilder(['q' => $query]);

        $msg = "Let me google that for you..\n" . $link;
        Utils\Response::sendResponse($msg, $in);

        return $link;
    }
}
